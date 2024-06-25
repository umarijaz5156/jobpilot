<?php

namespace Modules\Plan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'description' => 'required',
            'price' => 'required|numeric',
            'job_limit' => ['required', 'numeric'],
            'featured_job_limit' => ['required', 'numeric'],
            'highlight_job_limit' => ['required', 'numeric'],
            'frontend_show' => ['required', 'numeric'],
            'candidate_cv_view_limit' => [Rule::requiredIf(! empty(request('candidate_cv_view_limitation') && request('candidate_cv_view_limitation') == 'limited'), 'required')],
        ];

        if ($this->method() == 'POST') {
            $rules['label'] = ['required', 'string', 'unique:plans,label'];
        } else {
            $rules['label'] = ['required', 'string', "unique:plans,label,{$this->plan->id}"];
        }

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
