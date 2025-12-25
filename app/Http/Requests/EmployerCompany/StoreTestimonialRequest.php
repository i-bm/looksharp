<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployerCompany;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole(UserRoleEnum::EMPLOYER->value);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_name' => ['required', 'string', 'max:255'],
            'employee_title' => ['nullable', 'string', 'max:255'],
            'testimonial' => ['required', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB max
            'is_featured' => ['nullable', 'boolean'],
        ];
    }
}
