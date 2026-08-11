<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

// Import all Models
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;

// Import all Observers
use App\Observers\CustomerObserver;
use App\Observers\LeadObserver;
use App\Observers\TaskObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS in production (Render, etc.)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Register all Observers consistently and cleanly
        Task::observe(TaskObserver::class);
        Customer::observe(CustomerObserver::class);
        Lead::observe(LeadObserver::class);
    }
}