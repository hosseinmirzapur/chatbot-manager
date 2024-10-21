<?php

namespace App\Http\Requests;

use App\Models\Corporate;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CorporateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return $this->isMethod('POST') ? $this->postRules() : $this->putRules();
    }

    /**
     * @return array
     */
    public function postRules(): array
    {
        return [
            'name'=>  ['required', Rule::unique('corporates', 'name')],
            'chat_bg' => ['sometimes', 'image'],
            'logo' => ['sometimes', 'image'],
            'status' => ['required', Rule::in(Corporate::STATUSES)]
        ];
    }

    /**
     * @return array
     */
    public function putRules(): array
    {
        return [
            'name'=>  ['nullable', Rule::unique('corporates', 'name')],
            'chat_bg' => ['sometimes', 'image'],
            'logo' => ['sometimes', 'image'],
            'status' => ['nullable', Rule::in(Corporate::STATUSES)]
        ];
    }
}
