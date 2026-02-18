<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Policies\CustomerPolicy;
use App\Policies\LeadPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Customer::class => CustomerPolicy::class,
        Lead::class     => LeadPolicy::class,
        Task::class     => TaskPolicy::class, // ✅ اضافه شد
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });
    }
}
