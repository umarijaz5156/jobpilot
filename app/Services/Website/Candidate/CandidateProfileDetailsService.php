<?php

namespace App\Services\Website\Candidate;

use App\Models\User;
use Carbon\Carbon;

class CandidateProfileDetailsService
{
    /**
     * Get candidate profile details
     */
    public function execute($request): array
    {
        $user = authUser();
        $user_plan = $user->company->userPlan;

        $candidate = User::where('username', $request->username)
            ->with(['contactInfo', 'socialInfo', 'candidate' => function ($query) {
                $query->with('experience', 'education', 'experiences', 'educations', 'profession', 'languages:id,name', 'skills', 'socialInfo')
                    ->withCount(['bookmarkCandidates as bookmarked' => function ($q) {
                        $q->where('company_id', currentCompany()->id);
                    }])
                    ->withCount(['already_views as already_view' => function ($q) {
                        $q->where('company_id', currentCompany()->id);
                    }]);
            }])->firstOrFail();

        $candidate->candidate->birth_date = Carbon::parse($candidate->candidate->birth_date)->format('d F, Y');

        if ($user_plan->candidate_cv_view_limitation == 'limited' && $request->count_view) {
            $company = auth()->user()->company;
            $cv_views = $company->cv_views; // get auth company all cv views
            $cv_view_exist = $cv_views->where('candidate_id', $candidate->candidate->id)->first(); // get specific view

            if (! $cv_view_exist) {
                // check view isn't exist
                isset($user_plan) ? $user_plan->decrement('candidate_cv_view_limit') : ''; // point reduce
                // and create view count item
                $company->cv_views()->create([
                    'candidate_id' => $candidate->candidate->id,
                    'view_date' => Carbon::parse(Carbon::now()),
                ]);
            }
        }

        $cv_limit_message = $user_plan->candidate_cv_view_limitation == 'limited' ? 'You have '.$user_plan->candidate_cv_view_limit.' cv views remaining.' : null;

        $languages = $candidate->candidate
            ->languages()
            ->pluck('name')
            ->toArray();
        $candidate_languages = $languages ? implode(', ', $languages) : '';

        $skills = $candidate->candidate->skills->pluck('name');
        $candidate_skills = $skills ? implode(', ', json_decode(json_encode($skills), true)) : '';

        return [
            'success' => true,
            'data' => $candidate,
            'skills' => $candidate_skills ?? '',
            'languages' => $candidate_languages ?? '',
            'profile_view_limit' => $cv_limit_message,
        ];
    }
}
