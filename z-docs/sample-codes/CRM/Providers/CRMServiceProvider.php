<?php

namespace Modules\CRM\Providers;

use Illuminate\Support\ServiceProvider;
// use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Gate;

class CRMServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->app->register(RouteServiceProvider::class);
    }
}
