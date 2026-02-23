<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'label',
        'group',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
