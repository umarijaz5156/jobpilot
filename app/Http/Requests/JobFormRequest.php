<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobFormRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $min = $this->input('min_salary');
        $max = $this->input('max_salary');

        if ($this->method() == 'PUT') {
            return [
                'title' => 'required|string|max:255',
                'company_id' => 'nullable',
                'company_name' => 'required_if:company_id,null',
                'category_id' => 'required|numeric',
                'role_id' => 'required|numeric',
                'experience' => 'required',
                'education' => 'required',
                'vacancies' => 'required|string',
                'job_type' => 'required',
                'salary_mode' => 'required',
                'custom_salary' => 'required_if:salary_mode,==,custom',
                'min_salary' => 'nullable|numeric|between:0,'.$max,
                'max_salary' => 'nullable|numeric|min:'.$min,
                'salary_type' => 'required',
                'deadline' => 'required',
                'description' => 'required|string|min:50',
                'apply_on' => 'nullable',
                'apply_email' => 'nullable|email',
                'apply_url' => 'nullable|url',
                'featured' => 'nullable|numeric',
                'highlight' => 'nullable|numeric',
                'is_remote' => 'nullable|numeric',
            ];
        } elseif ($this->method() == 'POST') {
            return [
                'title' => 'required|string|max:255',
                'company_name' => 'required_if:company_id,null',
                'company_id' => 'required_if:company_name,null',
                'category_id' => 'required|numeric',
                'role_id' => 'required|numeric',
                'experience' => 'required',
                'education' => 'required|numeric',
                'vacancies' => 'required|string',
                'job_type' => 'nullable',
                'salary_mode' => 'required',
                'custom_salary' => 'required_if:salary_mode,==,custom',
                'min_salary' => 'nullable|numeric|between:0,'.$max,
                'max_salary' => 'nullable|numeric|min:'.$min,
                'salary_type' => 'nullable',
                'deadline' => 'required',
                'description' => 'required|string|min:50',
                'apply_on' => 'nullable',
                'apply_email' => 'nullable|email',
                'apply_url' => 'nullable|url',
                'featured' => 'nullable|numeric',
                'highlight' => 'nullable|numeric',
                'is_remote' => 'nullable|numeric',
                'location' => Rule::requiredIf(! session('location')),
            ];
        }
    }
}
