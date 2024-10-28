<?php

namespace App\Http\Controllers\website;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Services\Website\IndexPageService;
use Illuminate\Http\Request;

class pagecontroller extends Controller
{



    public function communications(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%communications%')->take(8)->get();
            return view('frontend.pages.content.communications', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function management(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%management%')->take(8)->get();
            return view('frontend.pages.content.management', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function resources(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '% human resources%')->take(8)->get();
            return view('frontend.pages.content.resources', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function environmental(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%environmental%')->take(8)->get();
            return view('frontend.pages.content.environmental', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function community(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%community%')->take(8)->get();
            return view('frontend.pages.content.community', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function accounting(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%accounting%')->take(8)->get();
            return view('frontend.pages.content.accounting', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function property(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%property%')->take(8)->get();
            return view('frontend.pages.content.property', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function economic(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%economic%')->take(8)->get();
            return view('frontend.pages.content.economic', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function planning(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%planning%')->take(8)->get();
            return view('frontend.pages.content.planning', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function asset(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%management%')->take(8)->get();
            return view('frontend.pages.content.asset', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function caravan(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%caravan%')->take(8)->get();
            return view('frontend.pages.content.caravan', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    public function corporation(){
        try {
            $data = (new IndexPageService())->execute();
            $data['electrical'] = Job::where('title', 'like', '%corporation%')->take(8)->get();
            return view('frontend.pages.content.corporation', $data);

        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }


}
