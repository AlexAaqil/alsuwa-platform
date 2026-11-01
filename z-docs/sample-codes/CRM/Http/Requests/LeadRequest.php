<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\CRM\Enums\CustomerType;
use Modules\CRM\Enums\LeadStatus;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization will be enforced by the CRM policies.
        return true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('POST');

        return [
            'type' => [
                ...($creating ? ['required'] : ['sometimes', 'required']),
                Rule::enum(CustomerType::class),
            ],
            'name' => [
                ...($creating ? ['required'] : ['sometimes', 'required']),
                'string',
                'max:255',
            ],
            'contact_person' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => [
                'sometimes',
                'required',
                Rule::enum(LeadStatus::class)->except([
                    LeadStatus::CONVERTED,
                ]),
            ],
            'notes' => ['sometimes', 'nullable', 'string'],
            'follow_up_at' => ['sometimes', 'nullable', 'date'],
            'assigned_to' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }
}
