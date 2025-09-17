<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    use HasFactory;

    public function order()
    {
        return $this->hasMany(\App\Models\Order::class, 'customer_id', 'id')
            ->orderBy('id', 'DESC')
            ->orderBy('date', 'DESC')
            ->take(10);
    }

    public function modJob()
    {
        return $this->hasOne(CustomerModJob::class);
    }
}
