<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Lead;

class LeadObserver
{
    private function resolveUserId(?int $fallbackOwnerId = null): ?int
    {
        return auth()->check() ? auth()->id() : $fallbackOwnerId;
    }

    private function onlyMeta(Lead $lead): array
    {
        return $lead->only([
            'name', 'email', 'phone', 'source', 'status',
            'owner_id', 'customer_id', 'converted_at'
        ]);
    }

    private function cleanChanges(array $changes): array
    {
        unset($changes['updated_at']);
        return $changes;
    }

    public function created(Lead $lead): void
    {
        $userId = $this->resolveUserId($lead->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'lead_created',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'meta' => $this->onlyMeta($lead),
        ]);
    }

    public function updated(Lead $lead): void
    {
        $changes = $this->cleanChanges($lead->getChanges());
        if (empty($changes)) return;

        $userId = $this->resolveUserId($lead->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'lead_updated',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'meta' => [
                'changes' => $changes,
            ],
        ]);
    }

    public function deleted(Lead $lead): void
    {
        $userId = $this->resolveUserId($lead->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'lead_deleted',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'meta' => $this->onlyMeta($lead),
        ]);
    }

    public function restored(Lead $lead): void
    {
        $userId = $this->resolveUserId($lead->owner_id);
        if (!$userId) return;

        ActivityLog::create([
            'user_id' => $userId,
            'action' => 'lead_restored',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'meta' => $this->onlyMeta($lead),
        ]);
    }
}
