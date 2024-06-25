<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use F9Web\ApiResponseHelpers;
use App\Http\Resources\Candidate\CandidateResource;
use App\Http\Resources\Company\CompanyResource;
use Firebase\Auth\Token\Exception\InvalidToken;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Contract\Auth;
use Kreait\Laravel\Firebase\Facades\Firebase;

class SocialAuthController extends Controller
{
    use ApiResponseHelpers;
    
    protected $auth;

    public function __construct()
    {
        $this->auth = Firebase::auth();
    }

    public function socialLogin(Request $request){
     
        $idTokenString = $request->firebasetoken;

        try {
            // Try to verify the Firebase credential token with Google
            $verifiedIdToken = $this->auth->verifyIdToken($idTokenString);
        } catch (\InvalidArgumentException $e) {
            // If the token has the wrong format
            return response()->json([
                'message' => 'Unauthorized - Can\'t parse the token: ' . $e->getMessage()
            ], 401);
        } catch (InvalidToken $e) {
            // If the token is invalid (expired ...)
            return response()->json([
                'message' => 'Unauthorized - Token is invalide: ' . $e->getMessage()
            ], 401);
        }

        // Retrieve the UID (User ID) from the verified Firebase credential's token
        $uid = $verifiedIdToken->claims()->get('sub');

        // Retrieve the user model linked with the Firebase UID
        $user = User::where('firebase_uid', $uid)->first();
        // $user = $this->auth->getUser($uid);

        // If the user doesn't exist, create a new user (you may customize this)
        try {
            if (!$user) {
                $user = User::create([
                    'firebase_uid' => $uid,
                    'role' => $request->input('role') ?? 'candidate',
                    'name' => $verifiedIdToken->getClaim('name'),
                    'email' => $verifiedIdToken->getClaim('email'),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
        
        // Create a Personal Access Token using Sanctum
        $token =  $user->createToken('job-pilot')->plainTextToken;

        return $this->respondWithSuccess([
            'data' => [
                'token' => $token,
                'message' => 'User data retrieved successfully',
                'user' => $user->role == 'candidate' ? new CandidateResource($user->candidate) : new CompanyResource($user->company)
            ]
        ]);
       
      
    }

}
