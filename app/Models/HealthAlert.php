<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthAlert extends Model
{
    protected $fillable = [
        'title', 'message', 'level', 'type', 'author_id', 'zone_id',
        'is_active', 'expires_at', 'target_roles', 'image',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'target_roles' => 'array',
        ];
    }

    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function zone() { return $this->belongsTo(Zone::class); }
}
