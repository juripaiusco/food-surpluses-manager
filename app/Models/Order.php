<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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

    public function retail()
    {
        return $this->hasOne(\App\Models\Retail::class, 'id', 'retail_id')->withTrashed();
    }
}
