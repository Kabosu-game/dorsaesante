<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    protected $fillable = [
        'user_id', 'type', 'description', 'latitude', 'longitude',
        'address', 'status', 'nearest_structure_id', 'resolved_at', 'responder_notes',
    ];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function nearestStructure() { return $this->belongsTo(HealthStructure::class, 'nearest_structure_id'); }
}
