<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LiveStream extends Model
{
    protected $fillable = [
        'doctor_id', 'title', 'description', 'stream_key', 'stream_url',
        'thumbnail', 'topic', 'status', 'scheduled_at', 'started_at',
        'ended_at', 'viewers_count', 'max_viewers', 'replay_url', 'is_recorded',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'is_recorded' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->stream_key = $model->stream_key ?? Str::random(32);
        });
    }

    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
}
