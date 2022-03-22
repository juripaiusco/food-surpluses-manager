<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function order()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id')
            ->where('date', 'LIKE', date('Y-m') . '%')
            ->orderBy('id', 'DESC')
            ->orderBy('date', 'DESC')
            ->take(10);
    }
}
