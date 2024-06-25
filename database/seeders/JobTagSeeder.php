<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class JobTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobs = Job::all();
        $tags = Tag::all();

        foreach ($jobs as $job) {
            $job->tags()->attach($tags->random(10));
        }
    }
}
