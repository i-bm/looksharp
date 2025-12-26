<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployerCompany;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
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
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:10240'], // 10MB max
            'caption' => ['nullable', 'string', 'max:255'],
        ];
    }
}
