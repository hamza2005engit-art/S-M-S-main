<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
        [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'section_id' => $this->section_id,
            'status' => $this->status,

            'student' => $this->when( $this->student, function () {
                return [
                    'student_number' => $this->student?->student_number,

                    'user'=>[
                        'full_name' => $this->student?->user?->full_name,
                        'phone' => $this->student?->user?->phone,
                        'profile_image' => $this->student?->user?->profile_image,
                    ]
                ];
            }),
        ];
    }
}
