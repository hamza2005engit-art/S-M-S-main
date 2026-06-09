<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'student_id' => $this->student_id,
            'total_amount' => $this->total_amount,
            'total_paid' => $this->total_paid,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'student' => new StudentResource($this->student),
            //'invoice_items' => InvoiceItemResource::collection($this->invoiceItems),
        ];
    }
}
