<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * (Always keep arrays neatly spaced for readability)
     */
    protected $fillable = [
        'title', 'notes', 'type', 'status', 'due_at',
        'assigned_to', 'lead_id', 'customer_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'due_at' => 'datetime',
    ];

    /**
     * Get the user who is assigned to this task.
     * We use BelongsTo type hinting for strict modern PHP.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the lead associated with this task.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the customer associated with this task.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope a query to only include tasks visible to a specific user.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        // 1. Admins see everything
        if ($user->hasRole('admin')) {
            return $query;
        }

        // 2. Sales users only see their own assigned tasks
        if ($user->hasRole('sales')) {
            return $query->where('assigned_to', $user->id);
        }

        // 3. SECURITY FIX: Default Deny. 
        // If they have any other role, they see NOTHING.
        return $query->whereRaw('1 = 0');
    }

    /**
     * Scope a query to only include trashed tasks visible to a specific user.
     */
    public function scopeTrashVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('sales')) {
            return $query->where('assigned_to', $user->id);
        }

        // Default Deny for the trash bin as well
        return $query->whereRaw('1 = 0');
    }
}