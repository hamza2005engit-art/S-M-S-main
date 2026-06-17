<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'full_name' => $this->full_name,

            'email' => $this->email,

            'phone' => $this->phone,

            'location' => $this->location,

            'approved' => $this->approved,

            'profile_image' => $this->profile_image,

            'role' => $this->getRoleNames()->first(),

            'teacher' => $this->teacher,

            'student' => $this->student,

            'admin' => $this->admin,

            'super_admin' => $this->superAdmin,

            'created_at' => $this->created_at,
        ];
    }
}