<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('leads.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        if (!$user->can('leads.view')) return false;

        if ($user->hasRole('admin')) return true;

        return $lead->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('leads.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        // converted قابل ویرایش نیست
        if ($lead->status === 'converted') return false;

        if (!$user->can('leads.update')) return false;

        if ($user->hasRole('admin')) return true;

        return $lead->owner_id === $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        if (!$user->can('leads.delete')) return false;

        if ($user->hasRole('admin')) return true;

        return $lead->owner_id === $user->id;
    }

    public function convert(User $user, Lead $lead): bool
    {
        if (!$user->can('leads.convert')) return false;

        if ($user->hasRole('admin')) return true;

        return $lead->owner_id === $user->id;
    }

        // ✅ دیدن صفحه‌ی Trash
    public function viewTrash(User $user): bool
    {
        return $user->can('leads.view') && ($user->hasRole('admin') || $user->hasRole('sales'));
    }

    // ✅ Restore
    public function restore(User $user, Lead $lead): bool
    {
        if (!$user->can('leads.update')) return false;

        if ($user->hasRole('admin')) return true;

        return $lead->owner_id === $user->id;
    }

    // ✅ Force Delete
    public function forceDelete(User $user, Lead $lead): bool
    {
        if (!$user->can('leads.delete')) return false;

        if ($user->hasRole('admin')) return true;

        return $lead->owner_id === $user->id;
    }


}
