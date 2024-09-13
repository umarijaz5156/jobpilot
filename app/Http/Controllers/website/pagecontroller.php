<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\Website\IndexPageService;
use Illuminate\Http\Request;

class pagecontroller extends Controller
{



    // public function mechanical(){
    //     try {
    //         $data = (new IndexPageService())->execute();
    //         $data['mechanical'] = Job::where('title', 'like', '%mechanical%')->take(8)->get();
    //         return view('frontend.pages.content.mechanical', $data);

    //     } catch (\Exception $e) {
    //         flashError('An error occurred: '.$e->getMessage());

    //         return back();
    //     }
    // }

}
