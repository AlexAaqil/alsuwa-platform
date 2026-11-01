
<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'

type Lead = {
    id: number
    uuid: string
    name: string
    type: string
    contact_person: string | null
    email: string | null
    phone: string | null
    source: string | null
    status: string
    follow_up_at: string | null
    assigned_to?: { id: number; name: string } | null
}

type PaginationLink = {
    url: string | null
    label: string
    active: boolean
}

const props = defineProps<{
    leads: {
        data: Lead[]
        links: PaginationLink[]
        from: number | null
        to: number | null
        total: number
    }
    filters: {
        search: string
        status: string
    }
    statuses: { value: string; label: string }[]
}>()

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
})

function applyFilters() {
    router.get('/crm/leads', {
        search: form.search || undefined,
        status: form.status || undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

function clearFilters() {
    form.search = ''
    form.status = ''
    applyFilters()
}

function deleteLead(lead: Lead) {
    if (! window.confirm(`Delete the lead "${lead.name}"?`)) {
        return
    }

    router.delete(`/crm/leads/${lead.id}`)
}

function humanize(value: string) {
    return value.replaceAll('_', ' ').replace(/\b\w/g, letter => letter.toUpperCase())
}
</script>

<template>
    <Head title="Leads" />

    <AppLayout>
        <div class="space-y-6 p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Leads</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage prospects, inquiries, and follow-ups.
                    </p>
                </div>

                <Link
                    href="/crm/leads/create"
                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700"
                >
                    Add lead
                </Link>
            </div>

            <form
                class="flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 p-4"
                @submit.prevent="applyFilters"
            >
                <div class="min-w-56 flex-1">
                    <label for="search" class="mb-1 block text-sm font-medium">
                        Search leads
                    </label>
                    <input
                        id="search"
                        v-model="form.search"
                        type="search"
                        placeholder="Name, email, phone..."
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    />
                </div>

                <div class="min-w-44">
                    <label for="status" class="mb-1 block text-sm font-medium">
                        Status
                    </label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option value="">All statuses</option>
                        <option
                            v-for="status in statuses"
                            :key="status.value"
                            :value="status.value"
                        >
                            {{ status.label }}
                        </option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium hover:bg-gray-50"
                >
                    Filter
                </button>

                <button
                    type="button"
                    class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900"
                    @click="clearFilters"
                >
                    Clear
                </button>
            </form>

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full min-w-[850px] divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Lead</th>
                            <th class="px-4 py-3 font-medium">Contact</th>
                            <th class="px-4 py-3 font-medium">Source</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Assigned to</th>
                            <th class="px-4 py-3 font-medium">Follow-up</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="lead in leads.data" :key="lead.id">
                            <td class="px-4 py-3">
                                <Link
                                    :href="`/crm/leads/${lead.id}`"
                                    class="font-medium text-gray-900 hover:underline"
                                >
                                    {{ lead.name }}
                                </Link>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ humanize(lead.type) }}
                                    <span v-if="lead.contact_person">
                                        · {{ lead.contact_person }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div>{{ lead.email || '—' }}</div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ lead.phone || '—' }}
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                {{ lead.source || '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700">
                                    {{ humanize(lead.status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                {{ lead.assigned_to?.name || 'Unassigned' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ lead.follow_up_at || '—' }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-3">
                                    <Link
                                        :href="`/crm/leads/${lead.id}/edit`"
                                        class="text-gray-700 hover:underline"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        class="text-red-600 hover:underline"
                                        @click="deleteLead(lead)"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="leads.data.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <p class="font-medium text-gray-900">No leads found</p>
                                <p class="mt-1">
                                    Add a lead or change your search filters.
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Showing {{ leads.from ?? 0 }}–{{ leads.to ?? 0 }}
                    of {{ leads.total }} leads
                </p>

                <div class="flex flex-wrap gap-1">
                    <template v-for="(link, index) in leads.links" :key="index">
                        <button
                            v-if="link.url"
                            type="button"
                            class="rounded border px-3 py-2 text-sm"
                            :class="link.active
                                ? 'border-gray-900 bg-gray-900 text-white'
                                : 'border-gray-200 hover:bg-gray-50'"
                            @click="router.get(link.url, {}, {
                                preserveState: true,
                                preserveScroll: true,
                            })"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded border border-gray-100 px-3 py-2 text-sm text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
