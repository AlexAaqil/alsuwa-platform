<script setup lang="ts">
import { computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import DashboardStat from './components/DashboardStat.vue';
import {
    Chart as ChartJS, Title, Tooltip, Legend, LineElement, CategoryScale,
    LinearScale, ArcElement, PointElement, DoughnutController, LineController,
} from 'chart.js';
import { Line, Doughnut } from 'vue-chartjs';

ChartJS.register(
    Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale,
    ArcElement, PointElement, DoughnutController, LineController,
);

const page = usePage();
const user = computed(() => page.props.auth.user);

interface Stats {
    counts: {
        total_customers: number;
        total_admins: number;
        total_sales_team: number;
    };
}

const props = defineProps<{ stats: Stats }>();
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="Dashboard space-y-8">
        <section class="header">
            <div class="flex items-center gap-4">
                <p>Hi {{ user.name }}</p>
                <span class="text-xs text-blue-900 bg-blue-100 py-1 px-2 rounded-sm">
                    {{ user.roles?.join(', ') }}
                </span>
            </div>
        </section>

        <section class="stats-wrapper">
            <h2 class="mb-4 font-medium">Platform Statistics</h2>
            
            <div class="stats grid gap-8 lg:grid-cols-6">
                <DashboardStat :stat="stats.counts.total_customers" label="Customers">
                    <template #extras>
                        <span class="text-sm text-muted-foreground">
                            {{ stats.counts.total_admins }} Admins & {{ stats.counts.total_sales_team }} Sales rep
                        </span>
                    </template>
                </DashboardStat>
            </div>
        </section>
    </div>
</template>