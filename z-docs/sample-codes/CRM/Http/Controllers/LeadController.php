<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\CRM\Enums\CustomerType;
use Modules\CRM\Enums\LeadStatus;
use Modules\CRM\Http\Requests\LeadRequest;
use Modules\CRM\Models\Lead;

class LeadController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->can('leads.view'), 403);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        $leads = Lead::query()
            ->with('assignedTo:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%")
                        ->orWhere('contact_person', 'ilike', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('crm/leads/Index', [
            'leads' => $leads,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
            'statuses' => collect(LeadStatus::cases())
                ->map(fn (LeadStatus $status) => [
                    'value' => $status->value,
                    'label' => str($status->value)->replace('_', ' ')->title()->toString(),
                ])
                ->values(),
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->can('leads.create'), 403);

        return Inertia::render('crm/leads/Create', [
            'types' => $this->customerTypes(),
            'statuses' => $this->leadStatuses(),
        ]);
    }

    public function store(LeadRequest $request): RedirectResponse
    {
        abort_unless($request->user()->can('leads.create'), 403);

        Lead::create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return to_route('crm.leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function show(Request $request, Lead $lead): Response
    {
        abort_unless($request->user()->can('leads.view'), 403);

        $lead->load([
            'assignedTo:id,name',
            'customer:id,uuid,name,type',
            'deals',
            'activities' => fn ($query) => $query
                ->with('assignedTo:id,name')
                ->latest(),
        ]);

        return Inertia::render('crm/leads/Show', [
            'lead' => $lead,
        ]);
    }

    public function edit(Request $request, Lead $lead): Response
    {
        abort_unless($request->user()->can('leads.update'), 403);

        return Inertia::render('crm/leads/Edit', [
            'lead' => $lead->load('assignedTo:id,name'),
            'types' => $this->customerTypes(),
            'statuses' => $this->leadStatuses(),
        ]);
    }

    public function update(LeadRequest $request, Lead $lead): RedirectResponse
    {
        abort_unless($request->user()->can('leads.update'), 403);

        $lead->update([
            ...$request->validated(),
            'updated_by' => $request->user()->id,
        ]);

        return to_route('crm.leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Request $request, Lead $lead): RedirectResponse
    {
        abort_unless($request->user()->can('leads.delete'), 403);

        $lead->delete();

        return to_route('crm.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    private function customerTypes(): array
    {
        return collect(CustomerType::cases())
            ->map(fn (CustomerType $type) => [
                'value' => $type->value,
                'label' => str($type->value)->replace('_', ' ')->title()->toString(),
            ])
            ->values()
            ->all();
    }

    private function leadStatuses(): array
    {
        return collect(LeadStatus::cases())
            ->reject(fn (LeadStatus $status) => $status === LeadStatus::CONVERTED)
            ->map(fn (LeadStatus $status) => [
                'value' => $status->value,
                'label' => str($status->value)->replace('_', ' ')->title()->toString(),
            ])
            ->values()
            ->all();
    }
}
