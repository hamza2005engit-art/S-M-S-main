<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeSalaryResource extends JsonResource
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
            'employeeable_id' => $this->employeeable_id,
            'employeeable_type' => $this->employeeable_type,
            'salary' => $this->salary,
            'date' => $this->date,
            'paid' => $this->paid,
            'paid_at' => $this->paid_at,
            'user' => new EmployeeResource($this->employeeable),
        ];
    }
}
