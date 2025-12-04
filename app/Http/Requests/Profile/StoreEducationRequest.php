<?php

namespace App\Http\Requests\Profile;

use App\Enums\DegreeTypeEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEducationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole(UserRoleEnum::TALENT->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'institution_id' => ['nullable', 'uuid', 'exists:institutions,id'],
            'degree_type' => ['required', Rule::enum(DegreeTypeEnum::class)],
            'field_of_study' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'is_current' => ['boolean'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'is_primary' => ['boolean'],
        ];
    }
}
