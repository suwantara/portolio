<?php

namespace App\Http\Requests;

use App\Models\Post;
use App\Rules\AssetUrl;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-blog') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                'alpha_dash',
                Rule::unique('posts', 'slug'),
            ],
            'category' => ['required', Rule::in(Post::CATEGORIES)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'cover_image' => ['nullable', 'string', 'max:2048', new AssetUrl],
            'content' => ['required', 'string', 'max:200000'],
            'status' => ['required', Rule::in(Post::STATUSES)],
        ];
    }
}
