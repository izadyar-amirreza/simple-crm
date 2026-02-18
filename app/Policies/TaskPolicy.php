<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('sales');
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) return true;
        return (int)$task->assigned_to === (int)$user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('sales');
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) return true;
        return (int)$task->assigned_to === (int)$user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->hasRole('admin')) return true;
        return (int)$task->assigned_to === (int)$user->id;
    }

    // برای صفحه Trash (اگر در Controller authorize می‌کنی)
    public function viewTrash(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function restore(User $user, Task $task): bool
    {
        // اگر بتواند delete کند، restore هم بتواند
        return $this->delete($user, $task);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        // پیشنهاد: فقط admin
        return $user->hasRole('admin');
    }
}
