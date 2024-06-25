<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPayperjobSettingUpdateRequest extends FormRequest
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
            'per_job_price' => 'required',
            'highlight_job_price' => 'required',
            'featured_job_price' => 'required',
            'highlight_job_days' => 'required|numeric|min:0',
            'featured_job_days' => 'required|numeric|min:0',
        ];
    }
}
