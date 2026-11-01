<?php

namespace Modules\Dashboard\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Modules\Order\Models\Order;

class SalesBucketer
{
    /**
     * Return a fixed-length array of totals for the given period.
     *
     * @return array{labels: string[], values: float[]}
     */
    public static function for(string $period, CarbonInterface $from, CarbonInterface $to): array
    {
        [$labels, $expr, $groupExpr, $range] = self::spec($period);

        $rows = Order::completed()
            ->whereBetween('sold_at', [$from, $to])
            ->selectRaw("{$expr} as bucket, COALESCE(SUM(total_selling_price), 0) as total")
            ->groupBy(DB::raw($groupExpr))
            ->pluck('total', 'bucket');

        $values = collect($range)
            ->map(fn ($k) => (float) ($rows[$k] ?? $rows[(string) $k] ?? 0))
            ->values()
            ->all();

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{0: string[], 1: string, 2: string, 3: int[]}
     */
    private static function spec(string $period): array
    {
        $driver = DB::connection()->getDriverName();
        $isSqlite = $driver === 'sqlite';

        return match ($period) {
            'today' => [
                array_map(fn ($h) => sprintf('%02d:00', $h), range(0, 23)),
                $isSqlite
                    ? "CAST(strftime('%H', sold_at) AS INTEGER)"
                    : "EXTRACT(HOUR FROM sold_at)::int",
                $isSqlite
                    ? "strftime('%H', sold_at)"
                    : "EXTRACT(HOUR FROM sold_at)",
                range(0, 23),
            ],
            'week' => [
                ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                $isSqlite
                    ? "CAST(strftime('%w', sold_at) AS INTEGER)"
                    : "EXTRACT(DOW FROM sold_at)::int",
                $isSqlite
                    ? "strftime('%w', sold_at)"
                    : "EXTRACT(DOW FROM sold_at)",
                // Postgres DOW: Sun=0..Sat=6. SQLite strftime %w: Sun=0..Sat=6.
                [1, 2, 3, 4, 5, 6, 0],
            ],
            'month' => [
                array_map(fn ($d) => (string) $d, range(1, 31)),
                $isSqlite
                    ? "CAST(strftime('%d', sold_at) AS INTEGER)"
                    : "EXTRACT(DAY FROM sold_at)::int",
                $isSqlite
                    ? "strftime('%d', sold_at)"
                    : "EXTRACT(DAY FROM sold_at)",
                range(1, 31),
            ],
            'year' => [
                ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                $isSqlite
                    ? "CAST(strftime('%m', sold_at) AS INTEGER)"
                    : "EXTRACT(MONTH FROM sold_at)::int",
                $isSqlite
                    ? "strftime('%m', sold_at)"
                    : "EXTRACT(MONTH FROM sold_at)",
                range(1, 12),
            ],
        };
    }
}