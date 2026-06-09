<?php

namespace App\Services\V1;

use App\Http\Resources\V1\ProfileResource;
use App\Http\Resources\V1\UserResource;

class ProfileService
{
    public function getProfile()
    {
        $user = auth('api')->user();

        if ($user->hasRole('student')) {
            $user->load([
                'student.section.studyStage'
            ]);

            return new ProfileResource($user);
        }
        return response()->json([
            'user'=> new UserResource($user)
        ]);
       
    }
}
