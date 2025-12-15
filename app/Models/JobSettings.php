<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSettings extends Model
{
    use HasFactory;

    protected $table = 'mod_jobs_settings';
    protected $fillable = [
        'type',
        'title',
        'schema',
        'dynamic',
    ];
}
