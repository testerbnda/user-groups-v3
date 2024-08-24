<?php

namespace App\Providers;
use Modules\Admin\Repositories\Interfaces\GroupInterface;
use Modules\Admin\Repositories\Implementations\GroupRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(GroupInterface::class, GroupRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
