<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
        'name',
        'email',
        'phone',
        'owner_id',
        'notes',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeVisibleTo($query, \App\Models\User $user)
    {
        // Admins can see all records
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Sales representatives can only see their own records
        if ($user->hasRole('sales')) {
            return $query->where('owner_id', $user->id);
        }

        // Support team can see all records
        if ($user->hasRole('support')) {
            return $query; 
        }

        // 🔒 Deny by default: Users with unknown roles see no records
        return $query->whereRaw('1=0'); 
    }

    public function scopeTrashVisibleTo($query, \App\Models\User $user)
    {
        // Currently, the Trash is restricted to admins only
        if ($user->hasRole('admin')) {
            return $query;
        }

        // Uncomment the line below if sales representatives should see their own trashed records:
        // return $query->where('owner_id', $user->id);

        // Deny by default: Show nothing to other roles
        return $query->whereRaw('1=0'); 
    }

}
