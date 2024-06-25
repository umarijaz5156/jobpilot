<?php

namespace App\Services\Website\Job;

use App\Http\Traits\JobAble;
use App\Models\Job;

class JobListService
{
    use JobAble;

    /**
     * Get job list
     *
     * @return void
     */
    public function jobs($request): mixed
    {
        $data = $this->getJobs($request);

        if ($data['total_jobs'] == 0 || $data['total_jobs'] < 18) {

            // Mix jobs (Careerjet + Indeed + App Jobs)
            $page = rand(1, 10000);
            $per_page = 18 - $data['total_jobs'];

            // Careerjet Jobs
            $careerjet_job_list = $this->getCareerjetJobs($request, $per_page, $page);
            $careerjet_jobs = $this->careerjetJobs($careerjet_job_list);

            // Indeed Jobs
            $indeed_job_list = $this->getIndeedJobs($request, $per_page, $page);
            $indeed_jobs = $this->indeedJobs($indeed_job_list);
        } else {
            $careerjet_jobs = [];
            $indeed_jobs = [];
        }

        if ($indeed_jobs && $careerjet_jobs) {
            $careerjet_indeed_jobs = $indeed_jobs->merge($careerjet_jobs);
        } elseif ($indeed_jobs) {
            $careerjet_indeed_jobs = $indeed_jobs;
        } elseif ($careerjet_jobs) {
            $careerjet_indeed_jobs = $careerjet_jobs;
        } else {
            $careerjet_indeed_jobs = [];
        }

        $careerjet_indeed_jobs = $careerjet_indeed_jobs;

        if ($careerjet_indeed_jobs && count($careerjet_indeed_jobs) > 0) {
            $all_jobs = $careerjet_indeed_jobs->merge($data['jobs']->items());
            $data['all_jobs'] = $all_jobs->reverse();
        } else {
            $data['all_jobs'] = [];
        }

        $data['resumes'] = $this->getResumes();

        return $data;
    }

    public function loadMore($request)
    {
        if ($request->page == 1) {
            $query = $this->filterJobs($request);
            $query->where('id', '<', $request->id);

            $jobs = $query->take(18)->latest()->get();

            return $jobs;
        } else {
            if (config('templatecookie.default_job_provider') == 'indeed') {
                $newJob = $this->indeedJobs($this->getIndeedJobs($request, 18, $request->page));
                if ($newJob->count()) {
                    return $newJob;
                } else {
                    return 0;
                }
            }
            if (config('templatecookie.default_job_provider') == 'careerjet') {
                $newJob = $this->careerjetJobs($this->getCareerjetJobs($request, 18, $request->page));

                if ($newJob->count()) {
                    return $newJob;
                } else {
                    return 0;
                }
            }
        }
    }

    public function categoryJobs($request, string $slug): mixed
    {
        $data = $this->getJobsCategory($request, $slug);

        // Mix jobs (Careerjet + Indeed + App Jobs)
        $page = rand(1, 10000);
        $per_page = null;

        $careerjet_job_list = $this->getCareerjetJobsCategory($request, $per_page, $page, $slug);
        $careerjet_jobs = $careerjet_jobs = $this->careerjetJobs($careerjet_job_list);

        $indeed_job_list = $this->getIndeedJobsCategory($request, $per_page, $page, $slug);
        $indeed_jobs = $this->indeedJobs($indeed_job_list);

        if ($indeed_jobs && $careerjet_jobs) {
            $careerjet_indeed_jobs = $indeed_jobs->merge($careerjet_jobs);
        } elseif ($indeed_jobs) {
            $careerjet_indeed_jobs = $indeed_jobs;
        } elseif ($careerjet_jobs) {
            $careerjet_indeed_jobs = $careerjet_jobs;
        } else {
            $careerjet_indeed_jobs = [];
        }

        $careerjet_indeed_jobs = $careerjet_indeed_jobs;

        if ($careerjet_indeed_jobs && count($careerjet_indeed_jobs) > 0) {
            $all_jobs = $careerjet_indeed_jobs->merge($data['jobs']->items());
            $data['all_jobs'] = $all_jobs->shuffle();
        } else {
            $data['all_jobs'] = [];
        }

        $data['resumes'] = $this->getResumes();

        return $data;
    }

    /**
     * Get resume list
     *
     * @param  $request
     */
    protected function getResumes(): mixed
    {
        if (auth('user')->check() && authUser()->role == 'candidate') {
            $resumes = currentCandidate()->resumes;
        } else {
            $resumes = [];
        }

        return $resumes;
    }

    /**
     * Get indeed jobs
     */
    protected function indeedJobs($indeed_job_list): mixed
    {
        $indeed_jobs = $indeed_job_list && isset($indeed_job_list->results)
            ? collect($indeed_job_list->results)->map(function ($jobs) {
                $jobs->job_provider = 'indeed';

                return $jobs;
            })
            : [];

        return $indeed_jobs;
    }

    /**
     * Get careerjet jobs
     */
    protected function careerjetJobs($careerjet_job_list): mixed
    {
        $careerjet_jobs = $careerjet_job_list && isset($careerjet_job_list->jobs)
            ? collect($careerjet_job_list->jobs)->map(function ($jobs) {
                $jobs->job_provider = 'careerjet';

                return $jobs;
            })
            : [];

        return $careerjet_jobs;
    }
}
