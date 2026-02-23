<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'street',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country',
        'is_default',
    ];

    public function users()
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }
}
