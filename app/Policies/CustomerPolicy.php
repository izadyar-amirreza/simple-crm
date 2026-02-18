<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    // لیست مشتری‌ها
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    // دیدن یک مشتری
    public function view(User $user, Customer $customer): bool
    {
        if ($user->hasRole('admin')) return $user->can('customers.view');

        return $user->can('customers.view') && $customer->owner_id === $user->id;
    }

    // ساخت
    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    // ویرایش
    public function update(User $user, Customer $customer): bool
    {
        if ($user->hasRole('admin')) return $user->can('customers.update');

        return $user->can('customers.update') && $customer->owner_id === $user->id;
    }

    // soft delete
    public function delete(User $user, Customer $customer): bool
    {
        if ($user->hasRole('admin')) return $user->can('customers.delete');

        return $user->can('customers.delete') && $customer->owner_id === $user->id;
    }

    // ✅ دیدن صفحه‌ی Trash (onlyTrashed list)
        public function viewTrash(User $user): bool
    {
        return $user->hasRole('admin') && $user->can('customers.view');
    }


    // ✅ Restore
    public function restore(User $user, Customer $customer): bool
    {
        if ($user->hasRole('admin')) return $user->can('customers.update');

        return $user->can('customers.update') && $customer->owner_id === $user->id;
    }

    // ✅ Force Delete
    public function forceDelete(User $user, Customer $customer): bool
    {
        if ($user->hasRole('admin')) return $user->can('customers.delete');

        return $user->can('customers.delete') && $customer->owner_id === $user->id;
    }
}
