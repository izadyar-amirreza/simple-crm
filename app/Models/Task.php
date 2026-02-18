<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title','notes','type','status','due_at',
        'assigned_to','lead_id','customer_id',
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin')) return $query;

        if ($user->hasRole('sales')) {
            return $query->where('assigned_to', $user->id);
        }

        return $query;
    }

        public function scopeTrashVisibleTo($query, User $user)
    {
        if ($user->hasRole('admin')) return $query;

        if ($user->hasRole('sales')) {
            return $query->where('assigned_to', $user->id);
        }

        return $query->whereRaw('1=0');
    }

}

