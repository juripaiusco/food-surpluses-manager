<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerModJob extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'schema', 'values'];

    protected $casts = [
        'schema' => 'array',
        'values' => 'array',
    ];

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
