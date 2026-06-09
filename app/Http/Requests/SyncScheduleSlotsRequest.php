<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SyncScheduleSlotsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
         return [

            'schedule_id' => [
                'required',
                'exists:schedules,id'
            ],

            'created_slots' => [
                'nullable',
                'array'
            ],

            'created_slots.*.day' => [
                'required',
                'in:sunday,monday,tuesday,wednesday,thursday',
            ],

            'created_slots.*.period_id' => [
                'required',
                'exists:periods,id'
            ],

            'created_slots.*.teacher_id' => [
                'required',
                'exists:teachers,id'
            ],

            'created_slots.*.material_id' => [
                'required',
                'exists:materials,id'
            ],

            'updated_slots' => [
                'nullable',
                'array'
            ],

            'updated_slots.*.id' => [
                'required',
                'exists:schedule_slots,id'
            ],

            'updated_slots.*.day' => [
                'required',
                'in:sunday,monday,tuesday,wednesday,thursday',
            ],

            'updated_slots.*.period_id' => [
                'required',
                'exists:periods,id'
            ],

            'updated_slots.*.teacher_id' => [
                'required',
                'exists:teachers,id'
            ],

            'updated_slots.*.material_id' => [
                'required',
                'exists:materials,id'
            ],

            'deleted_slot_ids' => [
                'nullable',
                'array'
            ],

            'deleted_slot_ids.*' => [
                'exists:schedule_slots,id'
            ],
        ];
    }
}
