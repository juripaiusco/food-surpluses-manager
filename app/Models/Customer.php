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

    public static function nextSuggestedNumber(): ?string
    {
        $last = self::orderBy('id', 'desc')->first();

        if (!$last || !ctype_digit((string) $last->number)) {
            return null;
        }

        return str_pad((string) ((int) $last->number + 1), strlen($last->number), '0', STR_PAD_LEFT);
    }
}
