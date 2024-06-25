<?php

namespace App\Http\Controllers\Website;

use App\Events\ChatMessage;
use App\Http\Controllers\Controller;
use App\Models\AppliedJob;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Messenger;
use App\Models\MessengerUser;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessengerController extends Controller
{
    public function companyMessages()
    {
        $users = $this->fetchCompanyUserList();
        $jobs = Job::where('company_id', currentCompany()->id)->active()->get(['id', 'title']);

        return view('frontend.pages.company.message', compact('users', 'jobs'));
    }

    public function candidateMessages()
    {
        $applied_jobs = AppliedJob::with('applicationGroup:id,name')->get();
        $applied_jobs->unique('job_id')->pluck('job_id');
        $jobs = Job::whereIn('id', $applied_jobs->pluck('job_id'))->active()->get(['id', 'title']);
        $users = $this->fetchCandidateUserList();

        return view('frontend.pages.company.message', compact('users', 'jobs'));
    }

    public function fetchMessages($username)
    {
        $user = User::whereUsername($username)->firstOrFail();

        if ($user->id != auth()->id()) {
            Messenger::where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('from', auth()->id());
                    $q->where('to', $user->id);
                })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('to', auth()->id());
                        $q->where('from', $user->id);
                    });
            })
                ->update(['read' => 1]);
        }

        return Messenger::where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('from', auth()->id());
                $q->where('to', $user->id);
            })
                ->orWhere(function ($q) use ($user) {
                    $q->where('to', auth()->id());
                    $q->where('from', $user->id);
                });
        })
            ->get();

        // return Messenger::where(function ($query) use ($user) {
        //     $query->where(function ($q) use ($user) {
        //         $q->where('from', auth()->id());
        //         $q->where('to', $user->id);
        //     })
        //         ->orWhere(function ($q) use ($user) {
        //             $q->where('to', auth()->id());
        //             $q->where('from', $user->id);
        //         });
        // })
        // ->latest()
        // ->get()
        // ->groupBy(function ($message) {
        //     return $message->created_at->format('d M, Y');
        // });
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate(['message' => 'required']);

            $message = Messenger::create([
                'from' => auth()->id(),
                'to' => $request->to,
                'body' => $request->message ?? 'No message',
                'messenger_user_id' => $request->chat_id,
                'read' => 0,
            ]);

            // $messages = Messenger::latest()->limit(1)->get()
            // ->groupBy(function ($message) {
            //     return $message->created_at->format('d M, Y');
            // });

            broadcast(new ChatMessage($message))->toOthers();

            return $message;
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function messageSendCandidate(Request $request)
    {
        $message_user_exists = MessengerUser::where('candidate_id', $request->candidate_id)
            ->where('company_id', currentCompany()->id)
            // ->where('job_id', $request->job_id)
            ->first();

        if (! $message_user_exists) {
            $message_user_exists = MessengerUser::create([
                'candidate_id' => $request->candidate_id,
                'company_id' => currentCompany()->id,
                'job_id' => $request->job_id ?? null,
            ]);
        }

        $message = Messenger::create([
            'from' => auth()->id(),
            'to' => Candidate::find($request->candidate_id)->user->id,
            'body' => $request->message ?? 'No message',
            'messenger_user_id' => $message_user_exists->id,
        ]);

        broadcast(new ChatMessage($message))->toOthers();

        if (isset($request->request_type) && $request->request_type == 'non_api') {
            flashSuccess('Message sent successfully');

            return back();
        }

        return response()->json(['success' => true]);
    }

    public function loadUnreadMessageCount()
    {
        if (auth()->check()) {
            $unread_message = Messenger::where('to', auth()->id())
                ->where('read', 0)
                ->count();
        } else {
            $unread_message = 0;
        }

        return $unread_message;
    }

    public function syncUserList()
    {
        $role = auth()->user()->role;

        if ($role == 'company') {
            $users = $this->fetchCompanyUserList();
        } elseif ($role == 'candidate') {
            $users = $this->fetchCandidateUserList();
        }

        return $users ?? [];
    }

    protected function fetchCompanyUserList()
    {
        $applied_jobs = AppliedJob::with('applicationGroup:id,name')->get();
        $all_users = MessengerUser::whereHas('messages')->with('candidate', 'job:id,title,slug')
            ->where('company_id', currentCompany()->id)
            ->withCount(['messages as latest_message_time' => function ($query) {
                $query->select(\DB::raw('max(created_at)'));
            }])
            ->orderByDesc('latest_message_time')
            ->get();

        $users = $all_users->unique('candidate_id')->map(function ($user) use ($applied_jobs) {
            $applied_job = $applied_jobs->where('candidate_id', $user->candidate_id)->where('job_id', $user->job_id)->first();

            $last_message = Messenger::candidateMessages($user)->latest()->first();

            if ($last_message) {
                $user->latest_message = $last_message->body;
                $diff_time = $last_message->created_at->diffForHumans(now(), CarbonInterface::DIFF_RELATIVE_AUTO, true, 1);
                $user->latest_message_humans_time = Str::of($diff_time)->replace(['from now', 'before'], '')->trim();

                $user->last_message_from_me = $last_message->from == auth()->id();
            }
            $user->unread_count = $this->getUnreadMessageCount($user->candidate_id);
            $user->application_status = $applied_job->applicationGroup->name ?? 'All Applications';

            return $user;
        });

        return $users;
    }

    protected function fetchCandidateUserList()
    {
        $all_users = MessengerUser::whereHas('messages')->with('company', 'job:id,title,slug')
            ->where('candidate_id', currentCandidate()->id)
            ->withCount(['messages as latest_message_time' => function ($query) {
                $query->select(\DB::raw('max(created_at)'));
            }])
            ->orderByDesc('latest_message_time')
            ->get();

        $users = $all_users->unique('company_id')->map(function ($user) {
            $last_message = Messenger::companyMessages($user)->latest()->first();

            if ($last_message) {
                $user->latest_message = $last_message->body;
                $diff_time = $last_message->created_at->diffForHumans(now(), CarbonInterface::DIFF_RELATIVE_AUTO, true, 1);
                $user->latest_message_humans_time = Str::of($diff_time)->replace(['before', 'after'], '')->trim();

                $user->last_message_from_me = $last_message->from == auth()->id();
            }

            $user->unread_count = $this->getUnreadMessageCount($user->company_id);

            return $user;
        });

        return $users;
    }

    public function filterUsers(Request $request)
    {
        if ($request->role == 'company') {
            $applied_jobs = AppliedJob::with('applicationGroup:id,name')->get();
            $all_users = MessengerUser::whereHas('messages')
                ->with('candidate', 'job:id,title,slug')
                ->where('company_id', currentCompany()->id)
                ->when($request->job, function ($query) use ($request) {
                    $query->where('job_id', $request->job);
                })
                ->withCount(['messages as latest_message_time' => function ($query) {
                    $query->select(\DB::raw('max(created_at)'));
                }])
                ->orderByDesc('latest_message_time')
                ->get();

            $users = $all_users->unique('candidate_id')
                ->map(function ($user) use ($applied_jobs) {
                    $applied_job = $applied_jobs->where('candidate_id', $user->candidate_id)
                        ->where('job_id', $user->job_id)
                        ->first();

                    $user->unread_count = Messenger::candidateMessages($user)->where('read', '!=', 1)->count() ?? 0;
                    $user->application_status = $applied_job->applicationGroup->name ?? 'All Applications';
                    $last_message = Messenger::select('id', 'from', 'to', 'body', 'created_at')->where(function ($query) use ($user) {
                        $query->where(function ($q) use ($user) {
                            $q->where('from', auth()->id());
                            $q->where('to', $user->candidate->user_id);
                        })->orWhere(function ($q) use ($user) {
                            $q->where('to', auth()->id());
                            $q->where('from', $user->candidate->user_id);
                        });
                    })
                        ->latest()
                        ->first();

                    if ($last_message) {
                        $user->latest_message = Str::limit($last_message->body, 15);
                        $diff_time = $last_message->created_at->diffForHumans(now(), CarbonInterface::DIFF_RELATIVE_AUTO, true, 1);
                        $user->latest_message_humans_time = Str::of($diff_time)->replace(['from now', 'before'], '')->trim();
                        $user->last_message_from_me = $last_message->from == auth()->id();
                    }

                    return $user;
                });

            return $users;
        } else {
            $applied_jobs = AppliedJob::with('applicationGroup:id,name')->get();
            $all_users = MessengerUser::whereHas('messages')
                ->with('company', 'job:id,title,slug')
                ->where('candidate_id', currentCandidate()->id)
                ->when($request->job, function ($query) use ($request) {
                    $query->where('job_id', $request->job);
                })
                ->withCount(['messages as latest_message_time' => function ($query) {
                    $query->select(\DB::raw('max(created_at)'));
                }])
                ->orderByDesc('latest_message_time')
                ->get()
                ->map(function ($user) use ($applied_jobs) {
                    $applied_job = $applied_jobs->where('candidate_id', $user->candidate_id)
                        ->where('job_id', $user->job_id)->first();
                    $user->application_status = $applied_job->applicationGroup->name ?? 'All Applications';

                    return $user;
                });

            $users = $all_users->unique('company_id');

            return $users;
        }
    }

    protected function getUnreadMessageCount($id)
    {
        $auth_user = auth()->user();

        if ($auth_user->role == 'company') {
            $user_messenger_id = MessengerUser::where('company_id', currentCompany()->id)->where('candidate_id', $id)->value('id');

            $unread_count = Messenger::where('messenger_user_id', $user_messenger_id)
                ->where('to', auth()->id())
                ->where('read', '!=', 1)
                ->count();
        } else {
            $user_messenger_id = MessengerUser::where('company_id', $id)->where('candidate_id', currentCandidate()->id)->value('id');

            $unread_count = Messenger::where('messenger_user_id', $user_messenger_id)
                ->where('to', auth()->id())
                ->where('read', '!=', 1)
                ->count();
        }

        return $unread_count ?? 0;
    }
}
