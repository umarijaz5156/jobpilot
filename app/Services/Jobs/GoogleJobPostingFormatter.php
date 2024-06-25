<?php

namespace App\Services\Jobs;

class GoogleJobPostingFormatter
{
    /**
     * Convert database job slug to Google employmentType
     *
     * @param  string  $type  Job type slug
     * @return string Valid employmentType
     *
     * @source Find more at -
     *      https://developers.google.com/search/docs/appearance/structured-data/job-posting#job-posting-definition
     */
    public static function formatJobType(string $type): string
    {
        $job_types = [
            'full-time' => 'FULL_TIME',
            'part-time' => 'PART_TIME',
            'contractual' => 'CONTRACTOR',
            'temporary' => 'TEMPORARY',
            'intern' => 'INTERN',
            'volunteer' => 'VOLUNTEER',
            'daily' => 'PER_DIEM',
            'freelance' => 'OTHER',
        ];

        return $job_types[$type] ?? '';
    }

    /**
     * Format salary type to Google salaryCurrency
     */
    public static function formatSalaryType($salary_type): mixed
    {
        $salary_types = [
            'hourly' => 'HOUR',
            'daily' => 'DAY',
            'weekly' => 'WEEK',
            'monthly' => 'MONTH',
            'yearly' => 'YEAR',
        ];

        return $salary_types[$salary_type] ?? '';
    }
}
