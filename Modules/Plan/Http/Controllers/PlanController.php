<?php

namespace Modules\Plan\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Modules\Language\Entities\Language;
use Modules\Plan\Entities\Plan;
use Modules\Plan\Entities\PlanDescription;

class PlanController extends Controller
{
    use ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        abort_if(! userCan('plan.view'), 403);

        $plans = Plan::get();
        $plan_descriptions = $plans->pluck('descriptions')->flatten();

        $current_language = currentLanguage();
        $current_language_code = $current_language ? $current_language->code : config('templatecookie.default_language');

        if ($current_language) {
            $plans->load(['descriptions' => function ($q) use ($current_language_code) {
                $q->where('locale', $current_language_code);
            }]);
        }

        return view('plan::index', compact('plans', 'current_language', 'plan_descriptions', 'current_language_code'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        abort_if(! userCan('plan.create'), 403);

        $app_languages = Language::latest()->get();

        return view('plan::create', compact('app_languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        abort_if(! userCan('plan.create'), 403);
        // validation
        $app_language = Language::latest()->get(['code', 'name']);

        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['description_'.$language->code] = 'required';
        }

        $validate_array['label'] = 'required|string|unique:plans,label';
        $validate_array['price'] = 'required|numeric';
        $validate_array['job_limit'] = 'required|numeric';
        $validate_array['featured_job_limit'] = 'required|numeric';
        $validate_array['highlight_job_limit'] = 'required|numeric';
        $validate_array['frontend_show'] = 'required|numeric';
        $validate_array['profile_verify'] = 'required|numeric';
        $validate_array['candidate_cv_view_limit'] = Rule::requiredIf(! empty(request('candidate_cv_view_limitation') && request('candidate_cv_view_limitation') == 'limited'), 'required');

        $this->validate($request, $validate_array);

        if ($request->candidate_cv_view_limitation == 'unlimited') {
            $request['candidate_cv_view_limit'] = 0;
        }

        $plan = Plan::create([
            'label' => $request->label,
            'price' => $request->price,
            'job_limit' => $request->job_limit,
            'featured_job_limit' => $request->featured_job_limit,
            'highlight_job_limit' => $request->highlight_job_limit,
            'candidate_cv_view_limitation' => $request->candidate_cv_view_limitation,
            'candidate_cv_view_limit' => (int) $request->candidate_cv_view_limitation == 'unlimited' ? 0 : $request->candidate_cv_view_limit,
            'frontend_show' => $request->frontend_show,
            'profile_verify' => $request->profile_verify,
            'recommended' => false,
        ]);

        $descriptions = $request->except(['_token', 'label', 'price', 'job_limit', 'featured_job_limit', 'highlight_job_limit', 'frontend_show', 'candidate_cv_view_limitation', 'candidate_cv_view_limit', 'profile_verify']);

        if ($descriptions && count($descriptions)) {
            foreach ($descriptions as $key => $description) {
                PlanDescription::create([
                    'plan_id' => $plan->id,
                    'locale' => str_replace('description_', '', $key),
                    'description' => $description,
                ]);
            }
        }

        flashSuccess(__('plan_created_successfully'));

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit(Plan $plan)
    {
        abort_if(! userCan('plan.update'), 403);

        $app_languages = Language::latest()->get();
        $plan->load('descriptions');

        return view('plan::edit', compact('plan', 'app_languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Renderable
     */
    public function update(Request $request, Plan $plan)
    {
        abort_if(! userCan('plan.update'), 403);

        // validation
        $app_language = Language::latest()->get(['code', 'name']);

        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['description_'.$language->code] = 'required';
        }

        $validate_array['label'] = "required|string|unique:plans,label,$plan->id";
        $validate_array['price'] = 'required|numeric';
        $validate_array['job_limit'] = 'required|numeric';
        $validate_array['featured_job_limit'] = 'required|numeric';
        $validate_array['highlight_job_limit'] = 'required|numeric';
        $validate_array['frontend_show'] = 'required|numeric';
        $validate_array['profile_verify'] = 'required|numeric';

        $validate_array['candidate_cv_view_limit'] = Rule::requiredIf(! empty(request('candidate_cv_view_limitation') && request('candidate_cv_view_limitation') == 'limited'), 'required');

        $this->validate($request, $validate_array);

        $plan->update([
            'label' => $request->label,
            'price' => $request->price,
            'job_limit' => $request->job_limit,
            'featured_job_limit' => $request->featured_job_limit,
            'highlight_job_limit' => $request->highlight_job_limit,
            'candidate_cv_view_limitation' => $request->candidate_cv_view_limitation,
            'candidate_cv_view_limit' => $request->candidate_cv_view_limitation == 'unlimited' ? 0 : $request->candidate_cv_view_limit,
            'frontend_show' => $request->frontend_show,
            'profile_verify' => $request->profile_verify,
            'recommended' => false,
        ]);

        $descriptions = $request->except(['_token', '_method', 'label', 'price', 'job_limit', 'featured_job_limit', 'highlight_job_limit', 'frontend_show', 'candidate_cv_view_limitation', 'candidate_cv_view_limit', 'profile_verify']);

        if ($descriptions && count($descriptions)) {
            $plan->descriptions()->delete();

            foreach ($descriptions as $key => $description) {
                PlanDescription::create([
                    'plan_id' => $plan->id,
                    'locale' => str_replace('description_', '', $key),
                    'description' => $description,
                ]);
            }
        }

        flashSuccess(__('plan_updated_successfully'));

        return redirect()->route('module.plan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(Plan $plan)
    {
        abort_if(! userCan('plan.delete'), 403);

        $plan->delete();

        flashSuccess(__('plan_deleted_successfully'));

        return back();
    }

    public function markRecommended()
    {
        abort_if(! userCan('plan.update'), 403);

        \DB::table('plans')->update(['recommended' => false]);
        Plan::findOrFail(request('plan_id'))->update(['recommended' => true]);

        flashSuccess(__('plan_updated_successfully'));

        return back();
    }

    public function markDefault(Request $request)
    {
        abort_if(! userCan('plan.update'), 403);

        Setting::first()->update([
            'default_plan' => $request->plan_id,
        ]);

        flashSuccess(__('default_plan_package_updated_successfully'));

        return back();
    }

    public function translateDescription()
    {
        // this is not implemented yet
        return true;
    }
}
