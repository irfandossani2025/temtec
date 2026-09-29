<?php

namespace App\Support;

use Illuminate\Validation\Rule;

class CompanyProfileRules
{
    /**
     * Validation rules for the client's company/contact details (shared by registration and profile).
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+()\-.\s]{6,50}$/'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ];
    }

    public static function serviceRules(): array
    {
        return [
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['integer', Rule::exists('services', 'id')->where('is_active', true)],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public static function fields(): array
    {
        return array_keys(self::rules());
    }
}
