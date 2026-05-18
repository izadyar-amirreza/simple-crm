<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Customer;
use App\Models\Lead;
use App\Observers\CustomerObserver;
use App\Observers\LeadObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Customer::observe(CustomerObserver::class);
        Lead::observe(LeadObserver::class);
    }
}