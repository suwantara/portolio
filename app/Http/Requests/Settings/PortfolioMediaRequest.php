<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PortfolioMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-portfolio') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kind' => ['required', Rule::in(['image', 'document'])],
            'file' => $this->input('kind') === 'image'
                ? ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192']
                : ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,csv', 'max:10240'],
        ];
    }
}
