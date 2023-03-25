<?php

namespace App\Models;

use App\Model\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function store()
    {
        return $this->hasMany(Store::class, 'product_id', 'id')
            ->orderBy('id', 'DESC')
            ->orderBy('date', 'DESC')
            ->take(10);
    }
}
