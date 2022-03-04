<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function store()
    {
        return $this->hasMany(Store::class, 'product_id', 'id');
    }
}
