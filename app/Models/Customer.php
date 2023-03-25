<?php

namespace App\Models;

use App\Model\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id')
            ->where('date', 'LIKE', date('Y-m') . '%')
            ->orderBy('id', 'DESC')
            ->orderBy('date', 'DESC')
            ->take(10);
    }
}
