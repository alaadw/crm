<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\StudentService;
use App\Services\PhoneService;
use App\Services\CategoryService;
use App\Services\FollowUpService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register PhoneService as singleton
        $this->app->singleton(PhoneService::class, function ($app) {
            return new PhoneService();
        });

        // Register CategoryService as singleton
        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService();
        });

        // Register FollowUpService as singleton
        $this->app->singleton(FollowUpService::class, function ($app) {
            return new FollowUpService();
        });

        // Register StudentService
        $this->app->bind(StudentService::class, function ($app) {
            return new StudentService(
                $app->make(PhoneService::class),
                $app->make(CategoryService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
