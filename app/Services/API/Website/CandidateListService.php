<?php

namespace App\Services\API\Website;

use App\Models\Candidate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use F9Web\ApiResponseHelpers;
use App\Http\Traits\Candidateable;
use App\Http\Traits\ResetCvViewsHistoryTrait;
use App\Http\Resources\Candidate\CandidateShortListResource;

class CandidateListService
{
    use Candidateable, ApiResponseHelpers, ResetCvViewsHistoryTrait;

    public function execute($request){
        if (auth('sanctum')->check() && auth('sanctum')->user()->role == 'candidate') {
            return $this->respondUnAuthenticated(__('unauthorized_access'));
        }

        $this->reset();

        if (auth('sanctum')->user() ? auth('sanctum')->user()->role == 'company' : '') {
            $query = Candidate::with(array('user' => function ($query) {
                $query->where('role', 'candidate');
            }))
                ->latest()
                ->with('user.contactInfo')
                ->withCount(['bookmarkCandidates as bookmarked' => function ($q) {
                    $q->where('company_id', auth('sanctum')->user()->company->id);
                }])
                ->withCount(['already_views as already_view' => function ($q) {
                    $q->where('company_id', auth('sanctum')->user()->company->id);
                }])
                ->withCasts(['already_view' => 'boolean'])
                ->with(['already_views' => function ($q) {
                    $q->where('company_id', auth('sanctum')->user()->company->id)->select(['candidate_id', 'company_id', 'view_date']);
                }])
                ->withCasts(['bookmarked' => 'boolean'])
                ->where('visibility', 1);
        } else {

            $query = Candidate::with(array('user' => function ($query) {
                $query->where('role', 'candidate');
            }))
                ->with('user.contactInfo')
                ->latest()
                ->where('visibility', 1);
        }

        // status
        if ($request->has('status') && $request->status != null) {
            $query->where('status', $request->status);
        }else{
            $query->where('status', 'available');
            $request['status'] = 'available';
        }

        // keyword
        if ($request->has('keyword') && $request->keyword != null) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%$request->keyword%");
            });
        }

        // location
        if ($request->has('lat') && $request->has('long') && $request->lat != null && $request->long != null) {
            $ids = $this->candidate_location_filter($request->lat, $request->long);
            $query->whereIn('id', $ids)->orWhere('country', $request->location ? $request->location : '');
        }


        // profession
        if ($request->has('profession') && $request->profession != null) {
            $query->where('profession_id', $request->profession);
        }

        // experience
        if ($request->has('experience') && $request->experience != null && $request->experience != 'all') {
            $experience_id = Experience::whereName($request->experience)->value('id');
            $query->where('experience_id', $experience_id);
        }

        // education
        if ($request->has('education') && $request->education != null && $request->education != 'all') {
            $education_id = Education::whereName($request->education)->value('id');
            $query->where('education_id', $education_id);
        }

        // gender
        if ($request->has('gender') && $request->gender != null) {
            $query->where('gender', request('gender'));
        }

        //  sortBy search
        if ($request->has('sortby') && $request->sortby) {
            if ($request->sortby == 'latest') {
                $query->latest();
            } else {
                $query->oldest();
            }
        }

         // languages filter
         if ($request->has('language') && $request->language != null) {
            $query->whereHas('languages', function($q) use ($request){
                $q->where('candidate_language.candidate_language_id', $request->language);
            });
        }

         // skills filter
         if ($request->has('skills') && $request->skills != null) {
            $skills = $request->skills;

            if($skills){
                $query->whereHas('skills', function($q) use ($skills){
                    $q->whereIn('candidate_skill.skill_id', $skills);
                });
            }
        }

        // perpage
        $candidates = $query->with('user', 'profession', 'experience');

        return CandidateShortListResource::collection($candidates->paginate(8)->withQueryString());
    }
}
