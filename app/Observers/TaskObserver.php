<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\ActivityLog;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'task_created',
            'subject_type' => Task::class,
            'subject_id'   => $task->id,
            'meta'         => [
                'task'    => $this->getTaskMeta($task),
                'task_id' => $task->id,
            ],
        ]);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Laravel automatically knows exactly which fields changed!
        $changes = $task->getChanges();
        $original = $task->getOriginal();
        
        $diff = [];
        
        foreach ($changes as $key => $newValue) {
            // We ignore 'updated_at' because it changes every time automatically
            if ($key === 'updated_at') {
                continue; 
            }
            
            $diff[$key] = [
                'from' => $original[$key] ?? null,
                'to'   => $newValue,
            ];
        }

        // Only log if there are actual data changes
        if (!empty($diff)) {
            ActivityLog::create([
                'user_id'      => auth()->id(),
                'action'       => 'task_updated',
                'subject_type' => Task::class,
                'subject_id'   => $task->id,
                'meta'         => [
                    'task'    => $this->getTaskMeta($task),
                    'task_id' => $task->id,
                    'changes' => $diff,
                ],
            ]);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => 'task_deleted',
            'subject_type' => Task::class,
            'subject_id'   => $task->id,
            'meta'         => [
                'task_id' => $task->id,
                'title'   => $task->title,
            ],
        ]);
    }

    /**
     * Helper method to keep the code DRY (Don't Repeat Yourself)
     */
    private function getTaskMeta(Task $task): array
    {
        return $task->only([
            'id', 'title', 'type', 'status', 'due_at',
            'assigned_to', 'lead_id', 'customer_id'
        ]);
    }
}