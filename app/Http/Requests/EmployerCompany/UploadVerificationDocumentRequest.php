<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployerCompany;

use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UploadVerificationDocumentRequest extends FormRequest
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
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB max
            'type' => ['required', 'string', 'in:ghana_card,business_registration'],
        ];
    }
}
