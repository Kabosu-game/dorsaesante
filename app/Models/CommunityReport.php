<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityReport extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'description', 'latitude', 'longitude',
        'address', 'zone_id', 'images', 'status', 'admin_response', 'is_anonymous',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_anonymous' => 'boolean',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function zone() { return $this->belongsTo(Zone::class); }
}
