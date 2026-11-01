<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\CRM\Enums\ActivityType;
use Modules\CRM\Models\Activity;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('POST');

        return [
            'type' => [
                ...($creating ? ['required'] : ['sometimes', 'required']),
                \Illuminate\Validation\Rule::enum(ActivityType::class),
            ],
            'subject' => [
                ...($creating ? ['required'] : ['sometimes', 'required']),
                'string',
                'max:255',
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_at' => ['sometimes', 'nullable', 'date'],
            'completed_at' => ['sometimes', 'nullable', 'date'],
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
            'deal_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:deals,id',
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
                $relationshipFields = [
                    'lead_id',
                    'customer_id',
                    'deal_id',
                ];

                $input = $this->all();

                $relationshipWasSubmitted = array_intersect(
                    $relationshipFields,
                    array_keys($input),
                ) !== [];

                if (! $creating && ! $relationshipWasSubmitted) {
                    return;
                }

                $activity = $this->route('activity');

                foreach ($relationshipFields as $field) {
                    $value = array_key_exists($field, $input)
                        ? $input[$field]
                        : ($activity instanceof Activity
                            ? $activity->{$field}
                            : null);

                    if (filled($value)) {
                        return;
                    }
                }

                $validator->errors()->add(
                    'lead_id',
                    'An activity must be linked to a lead, customer, or deal.',
                );
            },
        ];
    }
}
