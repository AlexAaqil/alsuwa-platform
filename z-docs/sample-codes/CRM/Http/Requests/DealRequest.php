<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\CRM\Enums\DealStage;
use Modules\CRM\Models\Deal;

class DealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('POST');

        return [
            'name' => [
                ...($creating ? ['required'] : ['sometimes', 'required']),
                'string',
                'max:255',
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'estimated_value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => [
                'sometimes',
                'string',
                'regex:/^[A-Z]{3}$/',
            ],
            'stage' => [
                'sometimes',
                'required',
                Rule::enum(DealStage::class)->only([
                    DealStage::QUALIFICATION,
                    DealStage::PROPOSAL,
                    DealStage::NEGOTIATION,
                ]),
            ],
            'expected_close_date' => ['sometimes', 'nullable', 'date'],
            'lead_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:leads,id',
            ],
            'customer_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:customers,id',
            ],
            'assigned_to' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $creating = $this->isMethod('POST');
                $relationshipFields = ['lead_id', 'customer_id'];

                $input = $this->all();
                $relationshipWasSubmitted = array_intersect(
                    $relationshipFields,
                    array_keys($input),
                ) !== [];

                if (! $creating && ! $relationshipWasSubmitted) {
                    return;
                }

                $deal = $this->route('deal');

                $leadId = array_key_exists('lead_id', $input)
                    ? $input['lead_id']
                    : ($deal instanceof Deal ? $deal->lead_id : null);

                $customerId = array_key_exists('customer_id', $input)
                    ? $input['customer_id']
                    : ($deal instanceof Deal ? $deal->customer_id : null);

                if (blank($leadId) && blank($customerId)) {
                    $validator->errors()->add(
                        'lead_id',
                        'A deal must be linked to a lead or a customer.',
                    );
                }
            },
        ];
    }
}
