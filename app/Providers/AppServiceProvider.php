<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        Customer::observe(CustomerObserver::class);
        Lead::observe(LeadObserver::class);
    }
}
