<?php

namespace App\Models;

use App\Model\Customer;
use App\Model\Retail;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function retail()
    {
        return $this->hasOne(Retail::class, 'id', 'retail_id')->withTrashed();
    }
}
