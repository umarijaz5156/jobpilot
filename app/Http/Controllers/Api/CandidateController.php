<?php

namespace App\Http\Controllers\Api;

use App\Models\Candidate;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use App\Models\CandidateResume;
use App\Http\Traits\CandidateAble;
use App\Http\Controllers\Controller;
use App\Http\Traits\CandidateSkillAble;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Candidate\CandidateResource;
use App\Services\API\Website\Candidate\FetchCandidateSettingService;
use App\Services\API\Website\Candidate\UpdateCandidateSettingService;

class CandidateController extends Controller
{
    use CandidateAble, CandidateSkillAble, ApiResponseHelpers;

    public function dashboard()
    {
        $candidate = Candidate::where('user_id', auth('sanctum')->id())->first();

        if (empty($candidate)) {
            $candidate = new Candidate();
            $candidate->user_id = auth('sanctum')->id();
            $candidate->save();
        }
        $data['profileComplated'] = $candidate->profile_complete;
        $data['appliedJobs'] = $candidate->appliedJobs->count();
        $data['favoriteJobs'] = $candidate->bookmarkJobs->count();
        $data['notifications'] = auth('sanctum')->user()->notifications()->count();

        return $this->respondWithSuccess([
            'data' => $data,
        ]);
    }

    public function candidate()
    {
        $candidate = Candidate::where('user_id', auth('sanctum')->id())->first();

        if (empty($candidate)) {

            $candidate = new Candidate();
            $candidate->user_id = auth('sanctum')->id();
            $candidate->save();
        }

        return $this->respondWithSuccess([
            'data' => new CandidateResource($candidate)
        ]);
    }

    public function fetchSettings(Request $request){
        return (new FetchCandidateSettingService())->execute($request);
    }

    public function updateSettings(Request $request)
    {
        return (new UpdateCandidateSettingService())->execute($request);
    }

    /**
     *  Candidate resume upload with normal form
     * @param $request
     */
    public function uploadResume(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'resume_file' => 'required|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        $candidate = auth('sanctum')->user()->candidate;
        $data['name'] = $request->name;
        $data['candidate_id'] = $candidate->id;

        // cv
        if ($request->resume_file) {
            $pdfPath = "file/candidates/";
            $file = uploadFileToPublic($request->resume_file, $pdfPath);
            $data['file'] = $file;
        }

        $resume = CandidateResume::create($data);

        return $this->respondWithSuccess([
            'data' => [
                'message' => 'Resume uploaded Successfully!',
                'data' => $resume,
            ]
        ]);
    }

    /**
     * Candidate all resume
     */
    public function getResumes()
    {
        if (auth('sanctum')->check() && auth('sanctum')->user()->role == 'candidate') {
            $resumes = auth('sanctum')->user()->candidate->resumes()->latest()->get()
                ->map(function ($item)
                {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'file' => $item->file,
                        'file_size' => $item->file_size,
                    ];
                });
        } else {
            $resumes = [];
        }

        return $this->respondWithSuccess([
            'data' => $resumes
        ]);
    }

    public function getResumeById($id)
    {
        $resume = CandidateResume::select(['id', 'name', 'file'])->findOrFail($id);

        return $this->respondWithSuccess([
           'data' => [
            'message' => 'Resume Retried Successfully!',
            'data' => $resume
           ]
        ]);
    }

    public function updateResume($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
        ]);

        $resume = CandidateResume::findOrFail($id);
        $data['name'] = $request->name;

        // cv
        if ($request->hasFile('resume_file')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100',
            ]);
            deleteFile($resume->file);
            $pdfPath = "file/candidates/";
            $file = uploadFileToPublic($request->resume_file, $pdfPath);
            $data['file'] = $file;
        }
        if ($validator->fails()) {
            return response()->json(
                ['errors' => $validator->messages()], 422
            );
        }

        $resume->update($data);

        return $this->respondWithSuccess([
           'data'=> [
                'message' => 'Resume Updated Successfully!',
                'data' => $resume,
           ]
        ]);
    }

    public function deleteResume($id)
    {
        $resume = CandidateResume::findOrFail($id);
        deleteFile($resume->file);
        $resume->delete();

        return $this->respondWithSuccess([
            'data'=> [
                'message' => 'Resume Deleted Successfully!',
                'status' => true
            ]
        ]);
    }
}
