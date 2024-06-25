<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminWPUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'working_process_step1_title' => 'required|string',
            'working_process_step1_description' => 'required|string',
            'working_process_step2_title' => 'required|string',
            'working_process_step2_description' => 'required|string',
            'working_process_step3_title' => 'required|string',
            'working_process_step3_description' => 'required|string',
            'working_process_step4_title' => 'required|string',
            'working_process_step4_description' => 'required|string',
        ];
    }
}
