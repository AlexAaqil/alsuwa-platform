<?php

namespace Modules\Dashboard\Support;

use Carbon\CarbonInterface;
use Modules\Order\Models\Order;

class PeriodStats
{
    /**
     * Compute completed-order aggregates for a date range.
     */
    public static function for(CarbonInterface $from, CarbonInterface $to): array
    {
        $summary = Order::completed()
            ->whereBetween('sold_at', [$from, $to])
            ->selectRaw('
                COUNT(*) as count,
                COALESCE(SUM(total_selling_price), 0) as revenue,
                COALESCE(SUM(total_cost_price), 0) as cogs
            ')
            ->first();

        $orders      = (int) $summary->count;
        $revenue     = (float) $summary->revenue;
        $cogs        = (float) $summary->cogs;
        $grossProfit = $revenue - $cogs;

        return [
            'orders'       => $orders,
            'revenue'      => $revenue,
            'cogs'         => $cogs,
            'gross_profit' => $grossProfit,
            'margin'       => $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0.0,
            'aov'          => $orders > 0 ? $revenue / $orders : 0.0,
        ];
    }

    /**
     * Percentage change between two values, safe against zero baselines.
     */
    public static function delta(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current > 0.0 ? 100.0 : null; // null => "n/a"
        }

        return (($current - $previous) / $previous) * 100;
    }
}