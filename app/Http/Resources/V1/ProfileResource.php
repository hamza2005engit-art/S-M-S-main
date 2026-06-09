<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_image' => $this->profile_image,
            'location' => $this->location,

            'roles' => $this->getRoleNames(),

            'student' => $this->when(
                $this->student,
                [
                    'student_number' => $this->student?->student_number,

                    'section' => [
                        'section_number' =>
                        $this->student?->section?->section_number,

                        'study_stage_number' =>
                        $this->student?->section?->studyStage?->stage_number,
                    ]
                ]
            ),
        ];
    }
}
