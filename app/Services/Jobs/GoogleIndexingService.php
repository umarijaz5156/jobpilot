<?php

namespace App\Services\Jobs;

use App\Models\Job;
use Google\Client;
use Illuminate\Support\Facades\Log;

class GoogleIndexingService
{
    /**
     * Notify Google Indexing about the job update
     *
     * @param  App\Models\Job  $job  Job being updated
     */
    public static function updateJobIndexing(Job $job): bool
    {
        $job_url = route('website.job.details', $job->slug);

        return self::notifyGoogleIndexing($job_url, 'URL_UPDATED');
    }

    /**
     * Notify Google Indexing about the job deletion
     *
     * @param  App\Models\Job  $job  Job being deleted
     * @return bool
     */
    public static function deleteJobIndexing(Job $job)
    {
        $job_url = route('website.job.details', $job->slug);

        return self::notifyGoogleIndexing($job_url, 'URL_DELETED');
    }

    /**
     * call Google Indexing API with JobPosting update
     *
     * @param  string  $job_url  URL of the jon in site
     * @param  string  $type  Type of request (Possibly, URL_UPDATED or URL_DELETED)
     * @return bool
     */
    private static function notifyGoogleIndexing(string $job_url, string $type = 'URL_UPDATED')
    {
        $exists = file_exists(resource_path('credentials.json'));
        if (! $exists) {
            return false;
        }

        try {
            $client = new Client();
            $client->setAuthConfig(resource_path('credentials.json'));
            $client->addScope('https://www.googleapis.com/auth/indexing');

            $httpClient = $client->authorize();
            $endpoint = 'https://indexing.googleapis.com/v3/urlNotifications:publish';

            $content = '{
                "url": "'.$job_url.'",
                "type": "'.$type.'"
            }';

            $response = $httpClient->post($endpoint, ['body' => $content]);
            $status_code = $response->getStatusCode();

            if ($status_code == 200) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return false;
        }
    }
}
