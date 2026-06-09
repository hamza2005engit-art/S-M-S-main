<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         $sectionId = $this->section_id;

         return [

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'students' => [
                'required',
                'array',
            ],

            'students.*.student_id' => [
                'required',
                  Rule::exists('students', 'id')
                    ->where(function ($query) use ($sectionId) {

                        $query->where(
                            'section_id',
                            $sectionId
                        );
                    }),
            ],

            'students.*.status' => [
                'required',
                Rule::in([
                    'present',
                    'absent',
                    'excused',
                ]),
            ],
        ];
    }
}
