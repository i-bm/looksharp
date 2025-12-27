<?php

declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\Enums\BillingCycleEnum;
use App\Enums\SubscriptionTierEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasRole('employer');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tier' => ['required', Rule::enum(SubscriptionTierEnum::class)],
            'billing_cycle' => [
                'required_if:tier,starter,professional',
                'nullable',
                Rule::enum(BillingCycleEnum::class),
            ],
        ];
    }
}
