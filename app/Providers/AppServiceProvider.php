<?php

namespace App\Providers;

use App\Models\Enquiry;
use App\Services\PlanPriceRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PlanPriceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.superadmin', function ($view) {
            try {
                $view->with('newEnquiries', Enquiry::query()->unread()->count());
            } catch (Throwable) {
                $view->with('newEnquiries', 0);
            }
        });
    }
}
