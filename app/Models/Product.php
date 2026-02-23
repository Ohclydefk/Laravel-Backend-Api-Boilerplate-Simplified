<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{   
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'stock',
        'is_active',
        'image',
    ];
}
