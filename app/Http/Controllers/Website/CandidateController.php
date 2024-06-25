<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Traits\CandidateAble;
use App\Http\Traits\CandidateSkillAble;
use App\Http\Traits\HasCandidateResume;
use App\Models\AppliedJob;
use App\Models\Candidate;
use App\Models\CandidateLanguage;
use App\Models\CandidateResume;
use App\Models\Company;
use App\Models\ContactInfo;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Profession;
use App\Models\Skill;
use App\Services\Website\Candidate\CandidateSettingUpdateService;
use App\Services\Website\Candidate\DashboardService;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    use CandidateAble, CandidateSkillAble, HasCandidateResume;

    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'settingUpdate',
        ]);
    }

    /**
     * Candidate dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        try {
            $data = (new DashboardService())->execute();

            return view('frontend.pages.candidate.dashboard', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate notification page
     *
     * @return \Illuminate\Http\Response
     */
    public function allNotification()
    {
        try {
            $notifications = auth()
                ->user()
                ->notifications()
                ->paginate(12);

            return view('frontend.pages.candidate.all-notification', compact('notifications'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate job alert page
     *
     * @return \Illuminate\Http\Response
     */
    public function jobAlerts()
    {
        try {
            $notifications = auth()
                ->user()
                ->notifications()
                ->where('type', 'App\Notifications\Website\Candidate\RelatedJobNotification')
                ->paginate(12);

            return view('frontend.pages.candidate.job-alerts', compact('notifications'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate applied job page
     *
     * @return \Illuminate\Http\Renderable
     */
    public function appliedjobs(Request $request)
    {
        try {
            $candidate = Candidate::where('user_id', auth()->id())->first();
            if (empty($candidate)) {
                $candidate = new Candidate();
                $candidate->user_id = auth()->id();
                $candidate->save();
            }

            $resumes = CandidateResume::where('candidate_id', $candidate->id)->get();
            $applied_jobs = AppliedJob::with('applicationGroup:id,name')
                ->where('candidate_id', $candidate->id)
                ->get();

            $appliedJobs = $candidate
                ->appliedJobs()
                ->paginate(8)
                ->through(function ($application) use ($applied_jobs, $resumes) {
                    $application_group = $applied_jobs->where('job_id', $application->id)->first();
                    $resume = $resumes->where('id', $application_group->candidate_resume_id)->first();
                    $application->application_status = $application_group->applicationGroup->name;
                    $application->cover_letter = $application_group->cover_letter;
                    $application->cv_file = $resume ? $resume->file : '';
                    $application->cv_name = $resume ? $resume->name : '';

                    return $application;
                });

            return view('frontend.pages.candidate.applied-jobs', compact('appliedJobs'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate bookmark page
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarks(Request $request)
    {
        try {
            $candidate = Candidate::where('user_id', auth()->id())->first();
            if (empty($candidate)) {
                $candidate = new Candidate();
                $candidate->user_id = auth()->id();
                $candidate->save();
            }

            $jobs = $candidate
                ->bookmarkJobs()
                ->withCount([
                    'appliedJobs as applied' => function ($q) use ($candidate) {
                        $q->where('candidate_id', $candidate->id);
                    },
                ])
                ->paginate(12);

            if (auth('user')->check() && authUser()->role == 'candidate') {
                $resumes = currentCandidate()->resumes;
            } else {
                $resumes = [];
            }

            return view('frontend.pages.candidate.bookmark', compact('jobs', 'resumes'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate bookmark company toggle
     *
     * @return \Illuminate\Http\Response
     */
    public function bookmarkCompany(Company $company)
    {
        try {
            $company->bookmarkCandidateCompany()->toggle(currentCandidate());

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function setting()
    {
        try {
            $candidate = auth()->user()->candidate;

            if (empty($candidate)) {
                Candidate::create(['user_id' => auth()->id()]);
            }

            // for contact
            $contactInfo = ContactInfo::where('user_id', auth()->id())->first();
            $contact = [];
            if ($contactInfo) {
                $contact = $contactInfo;
            } else {
                $contact = '';
            }

            // for social link
            $socials = auth()->user()->socialInfo;

            // for candidate resume/cv
            $resumes = $candidate->resumes;

            $job_roles = JobRole::all()->sortBy('name');
            $experiences = Experience::all();
            $educations = Education::all();
            $professions = Profession::all()->sortBy('name');
            $skills = Skill::all()->sortBy('name');
            $languages = CandidateLanguage::all(['id', 'name']);
            $candidate->load('skills', 'languages', 'experiences', 'educations', 'jobRoleAlerts:id,candidate_id,job_role_id');

            return view('frontend.pages.candidate.setting', [
                'candidate' => $candidate->load('skills', 'languages'),
                'contact' => $contact,
                'socials' => $socials,
                'job_roles' => $job_roles,
                'experiences' => $experiences,
                'educations' => $educations,
                'professions' => $professions,
                'resumes' => $resumes,
                'skills' => $skills,
                'candidate_languages' => $languages,
            ]);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate setting update
     *
     * @return \Illuminate\Http\Response
     */
    public function settingUpdate(Request $request)
    {
        try {
            (new CandidateSettingUpdateService())->update($request);

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Candidate username update
     *
     * @return \Illuminate\Http\Response
     */
    public function usernameUpdate(Request $request)
    {
        try {
            $request->session()->put('type', 'account');

            if ($request->type == 'candidate_username') {
                $request->validate([
                    'username' => 'required|unique:users,username,'.auth()->user()->id,
                ]);

                authUser()->update([
                    'username' => $request->username,
                ]);

                flashSuccess(__('username_updated_successfully'));

                return back();
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
