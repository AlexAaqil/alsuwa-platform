<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Dashboard\Support\PeriodStats;
use Modules\Dashboard\Support\SalesBucketer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonImmutable;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Payment\Models\Payment;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\User\Enums\UserRoles;
use Modules\User\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var \Modules\User\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            $stats = $this->adminStats($request->query('period', 'month'));
            $stats['system'] = $this->systemStats();

            return inertia('app/dashboards/SuperAdmin', [
                'user'  => $user,
                'stats' => $stats,
            ]);
        }

        if ($user->hasRole('admin')) {
            return inertia('app/dashboards/Admin', [
                'user'  => $user,
                'stats' => $this->adminStats($request->query('period', 'month')),
            ]);
        }

        if ($user->hasRole('cashier')) {
            return inertia('app/dashboards/Cashier', [
                'user'  => $user,
                'stats' => [],
            ]);
        }

        if ($user->hasRole('customer')) {
            return inertia('app/dashboards/Customer', [
                'user'  => $user,
                'stats' => [],
            ]);
        }

        return inertia('app/dashboards/Dashboard');
    }

    private function adminStats(string $period): array
    {
        $counts = [
            // 'total_users'    => User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'))->count(),
            'total_customers' => User::role('customer')->count(),
            'total_admins'   => User::role('admin')->count(),
            'total_sales_team' => User::role('sales')->count(),
        ];

        return [
            'counts' => $counts
        ];
    }

    private function systemStats(): array
    {
        $laravelVersion = app()->version();          // e.g. "11.31.0"
        $phpVersion     = PHP_VERSION;               // e.g. "8.3.12"

        // Composer lock gives exact installed package versions.
        $composerLock = base_path('composer.lock');
        $packages = [];
        if (is_file($composerLock)) {
            $lock = json_decode(file_get_contents($composerLock), true);
            // Grab a few packages you actually care about.
            $tracked = [
                'laravel/framework', 
                'inertiajs/inertia-laravel', 
                'intervention/image'
            ];
            foreach ($lock['packages'] ?? [] as $pkg) {
                if (in_array($pkg['name'], $tracked, true)) {
                    $packages[$pkg['name']] = $pkg['version'];
                }
            }
        }

        return [
            'app' => [
                'name'        => config('app.name'),
                'env'         => app()->environment(),          // "production", "local"
                'debug'       => (bool) config('app.debug'),
                'url'         => config('app.url'),
                'timezone'    => config('app.timezone'),
            ],
            'versions' => [
                'laravel' => $laravelVersion,
                'php'     => $phpVersion,
                'sapi'    => PHP_SAPI,                          // "fpm-fcgi", "cli-server"
            ],
            'runtime' => [
                'memory_usage' => memory_get_usage(true),
                'memory_peak'  => memory_get_peak_usage(true),
                'opcache'      => function_exists('opcache_get_status')
                    && (opcache_get_status(false)['opcache_enabled'] ?? false),
            ],
            'packages' => $packages,
            'server' => [
                'os'       => PHP_OS_FAMILY,                    // "Linux", "Darwin"
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
            ],
            // Cheap DB health signal — time a trivial query.
            'database' => $this->databaseHealth(),
        ];
    }

    private function databaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $ms = (microtime(true) - $start) * 1000;

            return [
                'ok'    => true,
                'driver'=> DB::connection()->getDriverName(),
                'ping_ms' => round($ms, 2),
            ];
        } catch (\Throwable $e) {
            return [
                'ok'     => false,
                'driver' => DB::connection()->getDriverName(),
                'error'  => $e->getMessage(),
            ];
        }
    }
}