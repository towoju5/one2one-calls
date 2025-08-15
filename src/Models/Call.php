<?php

namespace Towoju\One2OneCalls\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Call extends Model
{
    protected $table = 'calls';

    protected $fillable = [
        'uuid',
        'caller_id',
        'receiver_id',
        'status',
        'started_at',
        'ended_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function caller(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'caller_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'receiver_id');
    }
}
