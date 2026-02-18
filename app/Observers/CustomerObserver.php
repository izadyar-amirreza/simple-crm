<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\ActivityLog;

class CustomerObserver
{
    public function created(Customer $customer): void
    {
        $userId = $this->resolveUserId($customer->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'customer_created',
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'meta' => [
                'name'     => $customer->name,
                'email'    => $customer->email,
                'phone'    => $customer->phone,
                'owner_id' => $customer->owner_id,
            ],
        ]);
    }

    public function updated(Customer $customer): void
    {
        $changes = $customer->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) return;

        $userId = $this->resolveUserId($customer->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'customer_updated',
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'meta' => [
                'changes' => $changes,
            ],
        ]);
    }

    public function deleted(Customer $customer): void
    {
        $userId = $this->resolveUserId($customer->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'customer_deleted',
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'meta' => [
                'deleted_at' => optional($customer->deleted_at)->toDateTimeString(),
            ],
        ]);
    }

    public function restored(Customer $customer): void
    {
        $userId = $this->resolveUserId($customer->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'customer_restored',
            'subject_type' => Customer::class,
            'subject_id' => $customer->id,
            'meta' => [],
        ]);
    }

    private function resolveUserId(?int $fallbackOwnerId = null): ?int
    {
        return auth()->check() ? auth()->id() : $fallbackOwnerId;
    }
}
