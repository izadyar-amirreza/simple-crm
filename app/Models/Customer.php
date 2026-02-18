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

        public function scopeVisibleTo($query, \App\Models\User $user)
    {
        if ($user->hasRole('admin')) return $query;

        // sales فقط رکوردهای خودش
        if ($user->hasRole('sales')) {
            return $query->where('owner_id', $user->id);
        }

        // support فقط مشاهده (اگر خواستی)
        return $query;
    }

    public function scopeTrashVisibleTo($query, \App\Models\User $user)
    {
        // با سیاست فعلی تو Trash فقط برای admin است
        if ($user->hasRole('admin')) return $query;

        // اگر خواستی sales هم Trash خودش را ببیند:
        // return $query->where('owner_id', $user->id);

        return $query->whereRaw('1=0'); // هیچ چیز نشان نده
    }


}
