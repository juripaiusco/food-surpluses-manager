<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function store()
    {
        return $this->hasMany(\App\Models\Store::class, 'product_id', 'id')
            ->orderBy('id', 'DESC')
            ->orderBy('date', 'DESC')
            ->take(10);
    }

    public function category()
    {
        return $this->hasOne(\App\Models\Category::class, 'id', 'category_id');
    }
}
