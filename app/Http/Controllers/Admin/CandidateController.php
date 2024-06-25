<?php

namespace App\Http\Controllers\Admin;

use App\Export\CandidateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use App\Models\CandidateCvView;
use App\Models\CandidateLanguage;
use App\Models\ContactInfo;
use App\Models\Education;
use App\Models\Experience;
use App\Models\JobRole;
use App\Models\Profession;
use App\Models\Setting;
use App\Models\Skill;
use App\Models\SkillTranslation;
use App\Models\User;
use App\Notifications\CandidateCreateApprovalPendingNotification;
use App\Notifications\CandidateCreateNotification;
use App\Notifications\UpdateCompanyPassNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Location\Entities\Country;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            abort_if(! userCan('candidate.view'), 403);

        $query = Candidate::withCount('appliedJobs')->with('user', 'jobRole');

            // verified status
            if ($request->has('ev_status') && $request->ev_status != null) {
                $ev_status = null;
                if ($request->ev_status == 'true') {
                    $query->whereHas('user', function ($q) {
                        $q->whereNotNull('email_verified_at');
                    });
                } else {
                    $query->whereHas('user', function ($q) {
                        $q->whereNull('email_verified_at');
                    });
                }
            }

            if ($request->keyword && $request->keyword != null) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%$request->keyword%")->orWhere('email', 'LIKE', "%$request->keyword%");
                });
            }

            // sortby
            if ($request->sort_by == 'latest' || $request->sort_by == null) {
                $query->latest();
            } else {
                $query->oldest();
            }

            $candidates = $query->paginate(10)->withQueryString();

            return view('backend.candidate.index', compact('candidates'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            abort_if(! userCan('candidate.create'), 403);

            $data['countries'] = Country::all();
            $data['job_roles'] = JobRole::all()->sortBy('name');
            $data['professions'] = Profession::all()->sortBy('name');
            $data['experiences'] = Experience::all();
            $data['educations'] = Education::all();
            $data['skills'] = Skill::all()->sortBy('name');
            $data['candidate_languages'] = CandidateLanguage::all(['id', 'name']);

            return view('backend.candidate.create', $data);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userCreate($request)
    {
        $request->validate([
            'username' => 'unique:users,username',
            'email' => 'unique:users,email',
        ]);

        try {
            $password = $request->password ?? Str::random(8);

            $data = User::create([
                'role' => 'candidate',
                'name' => $request->name,
                'username' => Str::slug('K'.$request->name.'122'),
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => bcrypt($password),
                'remember_token' => Str::random(10),
            ]);

            return [$password, $data];
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $data
     * @return \Illuminate\Http\Response
     */

    // public function candidateCreate($request, $data)
    // {
    //     $dateTime = Carbon::parse($request->birth_date);
    //     $date = $request['birth_date'] = $dateTime->format('Y-m-d H:i:s');

    //     // create candidate
    //     $candidate = Candidate::where('user_id', $data[1]->id)->first();
    //     $candidate->update([
    //         'role_id' => $request->role_id,
    //         'profession_id' => $request->profession_id,
    //         'experience_id' => $request->experience,
    //         'education_id' => $request->education,
    //         'gender' => $request->gender,
    //         'website' => $request->website,
    //         'bio' => $request->bio,
    //         'marital_status' => $request->marital_status,
    //         'birth_date' => $date,
    //     ]);

    //     // cv upload
    //     if ($request->cv) {
    //         $pdfPath = '/file/candidates/';
    //         $pdf = pdfUpload($request->cv, $pdfPath);
    //         $candidate->update(['cv' => $pdf]);
    //     }

    //     // image upload
    //     if ($request->image) {
    //         $path = 'images/candidates';
    //         $image = uploadImage($request->image, $path);
    //     } else {
    //         $image = createAvatar($data['name'], 'uploads/images/candidate');
    //     }

    //     $candidate->update(['photo' => $image]);

    //     // skills insert
    //     $skills = $request->skills;

    //     if ($skills) {
    //         $skillsArray = [];

    //         foreach ($skills as $skill) {
    //             $skill_exists = Skill::where('id', $skill)->orWhere('name', $skill)->first();

    //             if (! $skill_exists) {
    //                 $select_tag = Skill::create(['name' => $skill]);
    //                 array_push($skillsArray, $select_tag->id);
    //             } else {
    //                 array_push($skillsArray, $skill);
    //             }
    //         }

    //         $candidate->skills()->attach($skillsArray);
    //     }

    //     // languages insert
    //     $candidate->languages()->attach($request->languages);

    //     return $candidate;
    // }

    public function candidateCreate($request, $data)
    {
        try {
            $dateTime = Carbon::parse($request->birth_date);
            $date = $request['birth_date'] = $dateTime->format('Y-m-d H:i:s');

            // create candidate
            $name = $request->name ?? fake()->name();
            $candidate = Candidate::where('user_id', $data[1]->id)->first();
            // $candidate->update([
            $candidate->update([
                'role_id' => $request->role_id,
                'profession_id' => $request->profession_id,
                'experience_id' => $request->experience,
                'education_id' => $request->education,
                'gender' => $request->gender,
                'website' => $request->website,
                'bio' => $request->bio,
                'marital_status' => $request->marital_status,
                'birth_date' => $date,
            ]);
            // Location
            updateMap($candidate);
            // cv upload
            if ($request->cv) {
                $pdfPath = '/file/candidates/';
                $pdf = pdfUpload($request->cv, $pdfPath);
                $candidate->update(['cv' => $pdf]);
            }

            // image upload
            if ($request->image) {
                // $path = 'images/candidates';
                $path = 'uploads/images/candidates';

                $image = uploadImage($request->image, $path, [164, 164]);
            } else {
                $setDimension = [164, 164];
                $path = 'uploads/images/candidates';

                $image = createAvatar($name, $path, $setDimension);
                // $image = createAvatar($data['name'], 'uploads/images/candidate');
            }

            $candidate->update(['photo' => $image]);

            // skills insert
            $skills = $request->skills;

            if ($skills) {
                $skillsArray = [];

                foreach ($skills as $skill) {
                    $skill_exists = Skill::where('id', $skill)->orWhere('name', $skill)->first();

                    if (! $skill_exists) {
                        $select_tag = Skill::create(['name' => $skill]);
                        array_push($skillsArray, $select_tag->id);
                    } else {
                        array_push($skillsArray, $skill);
                    }
                }

                $candidate->skills()->attach($skillsArray);
            }

            // languages insert
            $candidate->languages()->attach($request->languages);

            return $candidate;
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CandidateRequest $request)
    {
        abort_if(! userCan('candidate.create'), 403);
        $location = session()->get('location');
        if (! $location) {
            $request->validate(['location' => 'required']);
        }

        // try {
        if ($request->image) {
            $request->validate(['image' => 'image|mimes:jpeg,png,jpg,gif']);
        }
        if ($request->cv) {
            $request->validate(['cv' => 'mimetypes:application/pdf']);
        }

        $data = $this->userCreate($request);
        $candidate = $this->candidateCreate($request, $data);
        $user = $data[1];
        $password = $data[0];

        // if mail is configured
        if (checkMailConfig()) {
            $candidate_account_auto_activation_enabled = Setting::where('candidate_account_auto_activation', 1)->count();

            // if candidate activation enabled, send account created mail
            // else, send will be activated mail.
            if ($candidate_account_auto_activation_enabled) {
                Notification::route('mail', $user->email)->notify(new CandidateCreateNotification($user, $password));
            } else {
                Notification::route('mail', $user->email)->notify(new CandidateCreateApprovalPendingNotification($user, $password));
            }
        }

        flashSuccess(__('candidate_created_successfully'));

        return redirect()->route('candidate.index');
        // } catch (\Throwable $th) {
        //     return redirect()
        //         ->back()
        //         ->with('error', config('app.debug') ? $th->getMessage() : 'Something went wrong');
        // }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($candidate)
    {
        try {
            abort_if(! userCan('candidate.view'), 403);

            $candidate = Candidate::with('skills', 'languages:id,name', 'profession')->findOrFail($candidate);
            $user = User::with('socialInfo', 'contactInfo')->findOrFail($candidate->user_id);
            $appliedJobs = $candidate->appliedJobs()->with('company.user', 'category', 'role')->get();
            $bookmarkJobs = $candidate->bookmarkJobs()->with('company.user', 'category', 'role')->get();

            return view('backend.candidate.show', compact('candidate', 'user', 'appliedJobs', 'bookmarkJobs'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Candidate $candidate)
    {
        try {
            abort_if(! userCan('candidate.update'), 403);

            $user = User::with('contactInfo')->findOrFail($candidate->user_id);
            $contactInfo = ContactInfo::where('user_id', $user->id)->first();
            $job_roles = JobRole::all()->sortBy('name');
            $professions = Profession::all()->sortBy('name');
            $experiences = Experience::all();
            $educations = Education::all();
            $skills = Skill::all()->sortBy('name');
            $candidate_languages = CandidateLanguage::all(['id', 'name']);
            $candidate->load('skills', 'languages:id,name');
            $lat = $candidate->lat ? floatval($candidate->lat) : floatval(setting('default_lat'));
            $long = $candidate->long ? floatval($candidate->long) : floatval(setting('default_long'));

            return view('backend.candidate.edit', compact('contactInfo', 'candidate', 'user', 'job_roles', 'professions', 'experiences', 'educations', 'skills', 'candidate_languages', 'lat', 'long'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */

    // public function update(Request $request, Candidate $candidate)
    // {
    //     abort_if(! userCan('candidate.update'), 403);

    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,'.$candidate->user_id,
    //     ]);

    //     // user update
    //     $user = User::FindOrFail($candidate->user_id);
    //     $user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //     ]);

    //     // candidate update
    //     $candidate->update([
    //         'role_id' => $request->role_id,
    //         'profession_id' => $request->profession,
    //         'experience_id' => $request->experience,
    //         'education_id' => $request->education,
    //         'gender' => $request->gender,
    //         'website' => $request->website,
    //         'bio' => $request->bio,
    //         'marital_status' => $request->marital_status,
    //         'birth_date' => date('Y-m-d', strtotime($request->birth_date)),
    //     ]);

    //     // password change
    //     if ($request->password) {
    //         $request->validate([
    //             'password' => 'required',
    //         ]);
    //         $user->update([
    //             'password' => bcrypt($request->password),
    //         ]);
    //     }

    //     // image upload
    //     if ($request->image) {
    //         $request->validate([
    //             'image' => 'image|mimes:jpeg,png,jpg,gif',
    //         ]);

    //         $old_photo = $candidate->photo;
    //         if (file_exists($old_photo)) {
    //             if ($old_photo != 'backend/image/default.png') {
    //                 unlink($old_photo);
    //             }
    //         }
    //         $path = 'images/candidates';
    //         $image = uploadImage($request->image, $path);

    //         $candidate->update([
    //             'photo' => $image,
    //         ]);
    //     }
    //     // cv
    //     if ($request->cv) {
    //         $request->validate([
    //             'cv' => 'mimetypes:application/pdf',
    //         ]);
    //         $pdfPath = '/file/candidates/';
    //         $pdf = pdfUpload($request->cv, $pdfPath);

    //         $candidate->update([
    //             'cv' => $pdf,
    //         ]);
    //     }

    //     // Location
    //     updateMap($candidate);

    //     // skills
    //     $skills = $request->skills;

    //     if ($skills) {
    //         $skillsArray = [];

    //         foreach ($skills as $skill) {
    //             $skill_exists = SkillTranslation::where('skill_id', $skill)->orWhere('name', $skill)->first();

    //             if (! $skill_exists) {
    //                 $select_tag = Skill::create(['name' => $skill]);

    //                 $languages = loadLanguage();
    //                 foreach ($languages as $language) {
    //                     $select_tag->translateOrNew($language->code)->name = $skill;
    //                 }
    //                 $select_tag->save();

    //                 array_push($skillsArray, $select_tag->id);
    //             } else {
    //                 array_push($skillsArray, $skill_exists->id);
    //             }
    //         }
    //         $candidate->skills()->sync($request->skills);
    //     }

    //     // languages
    //     $candidate->languages()->sync($request->languages);

    //     if ($request->password) {
    //         // make Notification
    //         $data[] = $user;
    //         $data[] = $request->password;
    //         $data[] = 'Candidate';

    //         checkMailConfig() ? Notification::route('mail', $user->email)->notify(new UpdateCompanyPassNotification($data)) : '';
    //     }

    //     flashSuccess(__('candidate_updated_successfully'));

    //     return back();
    // }

    public function update(Request $request, Candidate $candidate)
    {
        try {
            abort_if(! userCan('candidate.update'), 403);

            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$candidate->user_id,
            ]);

            // user update
            $user = User::FindOrFail($candidate->user_id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // candidate update
            $candidate->update([
                'role_id' => $request->role_id,
                'profession_id' => $request->profession,
                'experience_id' => $request->experience,
                'education_id' => $request->education,
                'gender' => $request->gender,
                'website' => $request->website,
                'bio' => $request->bio,
                'marital_status' => $request->marital_status,
                'birth_date' => date('Y-m-d', strtotime($request->birth_date)),
            ]);

            // password change
            if ($request->password) {
                $request->validate([
                    'password' => 'required',
                ]);
                $user->update([
                    'password' => bcrypt($request->password),
                ]);
            }

            // image upload
            if ($request->image) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg',
                ]);

                deleteImage($candidate->photo);

                $path = 'uploads/images/candidates';
                $image = uploadImage($request->image, $path, [164, 164]);

                $candidate->update([
                    'photo' => $image,
                ]);
            }
            // cv
            if ($request->cv) {
                $request->validate([
                    'cv' => 'mimetypes:application/pdf',
                ]);
                $pdfPath = '/file/candidates/';
                $pdf = pdfUpload($request->cv, $pdfPath);

                $candidate->update([
                    'cv' => $pdf,
                ]);
            }

            // Location
            updateMap($candidate);

            // skills
            $skills = $request->skills;

            if ($skills) {
                $skillsArray = [];

                foreach ($skills as $skill) {
                    $skill_exists = SkillTranslation::where('skill_id', $skill)->orWhere('name', $skill)->first();

                    if (! $skill_exists) {
                        $select_tag = Skill::create(['name' => $skill]);

                        $languages = loadLanguage();
                        foreach ($languages as $language) {
                            $select_tag->translateOrNew($language->code)->name = $skill;
                        }
                        $select_tag->save();

                        array_push($skillsArray, $select_tag->id);
                    } else {
                        array_push($skillsArray, $skill_exists->id);
                    }
                }
                $candidate->skills()->sync($request->skills);
            }

            // languages
            $candidate->languages()->sync($request->languages);

            if ($request->password) {
                // make Notification
                $data[] = $user;
                $data[] = $request->password;
                $data[] = 'Candidate';

                checkMailConfig() ? Notification::route('mail', $user->email)->notify(new UpdateCompanyPassNotification($data)) : '';
            }

            flashSuccess(__('candidate_updated_successfully'));

            return back();
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Candidate $candidate)
    {
        try {
            abort_if(! userCan('candidate.delete'), 403);

            $user = User::FindOrFail($candidate->user_id);
            CandidateCvView::query()
                ->where('candidate_id', $candidate->id)
                ->delete();
            $user->delete();

            if (file_exists($candidate->cv)) {
                unlink($candidate->cv);
            }

            if (file_exists($candidate->photo)) {
                if ($candidate->photo != 'backend/image/default.png') {
                    unlink($candidate->photo);
                }
            }
            $candidate->delete();

            flashSuccess(__('candidate_deleted_successfully'));

            return redirect()->route('candidate.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change candidate status
     *
     * @return \Illuminate\Http\Response
     */
    public function statusChange(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->status = $request->status;
            $user->save();

            if ($request->status == 1) {
                return responseSuccess(__('candidate_activated_successfully'));
            } else {
                return responseSuccess(__('candidate_deactivated_successfully'));
            }
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Change candidate verification status
     *
     * @return \Illuminate\Http\Response
     */
    public function verificationChange(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($request->status) {
                $user->update(['email_verified_at' => now()]);
                $message = __('email_verified_successfully');
            } else {
                $user->update(['email_verified_at' => null]);
                $message = __('email_unverified_successfully');
            }

            return responseSuccess($message);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function candidateExport($type)
    {
        $name = time().'_candidates.'.$type;
        try {
            return Excel::download(new CandidateExport(), $name);
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }
}
