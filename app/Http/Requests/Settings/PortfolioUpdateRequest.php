<?php

namespace App\Http\Requests\Settings;

use App\Rules\AssetUrl;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PortfolioUpdateRequest extends FormRequest
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
            'profile.name' => ['required', 'string', 'max:120'],
            'profile.monogram' => ['required', 'string', 'max:4'],
            'profile.role' => ['required', 'string', 'max:160'],
            'profile.tagline' => ['required', 'string', 'max:500'],
            'profile.location' => ['required', 'string', 'max:160'],
            'profile.timezone' => ['required', 'string', 'max:120'],
            'profile.email' => ['required', 'email', 'max:255'],
            'profile.availability' => ['required', 'string', 'max:120'],
            'profile.availability_note' => ['required', 'string', 'max:300'],
            'profile.availability_open' => ['required', 'boolean'],
            'profile.avatar' => ['nullable', 'string', 'max:2048', new AssetUrl],

            'stats' => ['array', 'max:6'],
            'stats.*.value' => ['required', 'string', 'max:20'],
            'stats.*.label' => ['required', 'string', 'max:60'],

            'about' => ['array', 'max:12'],
            'about.*' => ['required', 'string', 'max:2000'],
            'about_tags' => ['array', 'max:20'],
            'about_tags.*' => ['required', 'string', 'max:60'],

            'experience' => ['array', 'max:30'],
            'experience.*.role' => ['required', 'string', 'max:160'],
            'experience.*.company' => ['required', 'string', 'max:160'],
            'experience.*.employment' => ['nullable', 'string', 'max:80'],
            'experience.*.period' => ['required', 'string', 'max:80'],
            'experience.*.location' => ['nullable', 'string', 'max:160'],
            'experience.*.description' => ['nullable', 'string', 'max:1000'],
            'experience.*.highlights' => ['array', 'max:20'],
            'experience.*.highlights.*' => ['required', 'string', 'max:500'],
            'experience.*.skills' => ['array', 'max:30'],
            'experience.*.skills.*' => ['required', 'string', 'max:60'],
            'experience.*.monogram' => ['required', 'string', 'max:4'],
            'experience.*.accent' => [
                'required',
                Rule::in(config('portfolio.options.experience_accents', [])),
            ],

            'projects' => ['array', 'max:20'],
            'projects.*.name' => ['required', 'string', 'max:160'],
            'projects.*.year' => ['required', 'string', 'max:20'],
            'projects.*.description' => ['required', 'string', 'max:1000'],
            'projects.*.tags' => ['array', 'max:20'],
            'projects.*.tags.*' => ['required', 'string', 'max:60'],
            'projects.*.cover' => [
                'required',
                Rule::in(config('portfolio.options.project_covers', [])),
            ],
            'projects.*.image' => ['nullable', 'string', 'max:2048', new AssetUrl],
            'projects.*.link' => ['nullable', 'url:http,https', 'max:2048'],
            'projects.*.featured' => ['required', 'boolean'],

            'gallery' => ['array', 'max:100'],
            'gallery.*.image' => ['required', 'string', 'max:2048', new AssetUrl],
            'gallery.*.title' => ['required', 'string', 'max:120'],
            'gallery.*.caption' => ['nullable', 'string', 'max:300'],
            'gallery.*.link' => ['nullable', 'url:http,https', 'max:2048'],
            'gallery.*.featured' => ['required', 'boolean'],

            'documents' => ['array', 'max:12'],
            'documents.*.title' => ['required', 'string', 'max:120'],
            'documents.*.description' => ['nullable', 'string', 'max:300'],
            'documents.*.file' => ['required', 'string', 'max:2048', new AssetUrl],

            'skill_groups' => ['array', 'max:12'],
            'skill_groups.*.title' => ['required', 'string', 'max:80'],
            'skill_groups.*.skills' => ['array', 'max:30'],
            'skill_groups.*.skills.*' => ['required', 'string', 'max:60'],

            'education' => ['array', 'max:12'],
            'education.*.degree' => ['required', 'string', 'max:160'],
            'education.*.school' => ['required', 'string', 'max:160'],
            'education.*.period' => ['required', 'string', 'max:80'],
            'education.*.description' => ['nullable', 'string', 'max:1000'],

            'languages' => ['array', 'max:20'],
            'languages.*.name' => ['required', 'string', 'max:80'],
            'languages.*.level' => ['required', 'string', 'max:80'],

            'currently_exploring' => ['array', 'max:30'],
            'currently_exploring.*' => ['required', 'string', 'max:60'],

            'socials' => ['array', 'max:8'],
            'socials.*.name' => [
                'required',
                Rule::in(config('portfolio.options.socials', [])),
            ],
            'socials.*.label' => ['required', 'string', 'max:60'],
            'socials.*.href' => ['required', 'url:http,https', 'max:2048'],

            'cta.eyebrow' => ['required', 'string', 'max:120'],
            'cta.heading' => ['required', 'string', 'max:200'],
            'cta.description' => ['required', 'string', 'max:500'],
            'cta.button' => ['required', 'string', 'max:60'],
            'cta.subject' => ['required', 'string', 'max:160'],

            'gallery_header.eyebrow' => ['required', 'string', 'max:80'],
            'gallery_header.title' => ['required', 'string', 'max:80'],
            'gallery_header.description' => ['required', 'string', 'max:500'],

            'blog_header.eyebrow' => ['required', 'string', 'max:80'],
            'blog_header.title' => ['required', 'string', 'max:80'],
            'blog_header.description' => ['required', 'string', 'max:500'],

            'footer_note' => ['required', 'string', 'max:200'],
        ];
    }
}
