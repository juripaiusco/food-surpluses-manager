<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function customer()
    {
        return $this->hasOne(\App\Models\Customer::class, 'id', 'customer_id');
    }

    public function order()
    {
        return $this->hasOne(\App\Models\Order::class, 'id', 'order_id');
    }
}
