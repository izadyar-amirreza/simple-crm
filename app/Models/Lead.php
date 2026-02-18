<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name','email','phone','source','status',
        'owner_id','converted_at','customer_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

        public function scopeVisibleTo($query, \App\Models\User $user)
    {
        if ($user->hasRole('admin')) return $query;

        if ($user->hasRole('sales')) {
            return $query->where('owner_id', $user->id);
        }

        return $query;
    }

        public function scopeTrashVisibleTo($query, \App\Models\User $user)
    {
        if ($user->hasRole('admin')) return $query;

        if ($user->hasRole('sales')) {
            return $query->where('owner_id', $user->id);
        }

        return $query->whereRaw('1=0');
    }



}
