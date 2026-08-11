<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobSettings extends Model
{
    use HasFactory;

    protected $table = 'mod_jobs_settings';
    protected $fillable = [
        'type',
        'title',
        'description',
        'query',
        'schema',
        'dynamic',
        'visible',
        'user_id',
        'uuid',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (JobSettings $jobSettings) {
            if (empty($jobSettings->uuid)) {
                $jobSettings->uuid = (string) Str::uuid();
            }
        });
    }
}
