<?php

namespace Modules\Testimonial\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Language\Entities\Language;
use Modules\Testimonial\Actions\CreateTestimonial;
use Modules\Testimonial\Actions\DeleteTestimonial;
use Modules\Testimonial\Actions\UpdateTestimonial;
use Modules\Testimonial\Entities\Testimonial;
use Modules\Testimonial\Http\Requests\TestimonialFormRequest;

class TestimonialController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['permission:testimonial.view'])->only('index');
        $this->middleware(['permission:testimonial.create'])->only(['create', 'store']);
        $this->middleware(['permission:testimonial.update'])->only(['edit', 'update']);
        $this->middleware(['permission:testimonial.create'])->only(['create', 'store']);
        $this->middleware(['permission:testimonial.update'])->only(['edit', 'update']);
        $this->middleware(['permission:testimonial.delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        try {
            $testimonials = Testimonial::latest()->get();
            $group_testimonials = $testimonials->groupBy('code');

            return view('testimonial::index', compact('testimonials', 'group_testimonials'));
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        try {
            $app_languages = Language::all(['id', 'name', 'code']);

            return view('testimonial::create', compact('app_languages'));
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(TestimonialFormRequest $request)
    {
        try {
            $testimonial = CreateTestimonial::create($request);

            if ($testimonial) {
                flashSuccess(__('testimonial_created_successfully'));

                return back();
            } else {
                flashError();

                return back();
            }
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit(Testimonial $testimonial)
    {
        try {
            $app_languages = Language::all(['id', 'name', 'code']);

            return view('testimonial::edit', compact('testimonial', 'app_languages'));
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(TestimonialFormRequest $request, Testimonial $testimonial)
    {
        try {
            $testimonial = UpdateTestimonial::update($request, $testimonial);

            if ($testimonial) {
                flashSuccess(__('testimonial_updated_successfully'));

                return redirect(route('module.testimonial.index'));
            } else {
                flashError();

                return back();
            }
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            $testimonial = DeleteTestimonial::delete($testimonial);

            if ($testimonial) {
                flashSuccess(__('testimonial_deleted_successfully'));

                return back();
            } else {
                flashError();

                return back();
            }
        } catch (\Exception $e) {

            flashError('An error occurred: '.$e->getMessage());

            return back();
        }

    }
}
