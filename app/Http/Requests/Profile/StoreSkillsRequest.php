<?php

namespace App\Http\Requests\Profile;

use App\Enums\ProficiencyLevelEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillsRequest extends FormRequest
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
            'skill_name' => ['required', 'string', 'max:255'],
            'proficiency_level' => ['required', Rule::enum(ProficiencyLevelEnum::class)],
        ];
    }
}
