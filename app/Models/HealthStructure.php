<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthStructure extends Model
{
    protected $fillable = [
        'name', 'type', 'description', 'address', 'latitude', 'longitude',
        'phone', 'email', 'website', 'zone_id', 'has_emergency',
        'has_teleconsult', 'opening_hours', 'is_active', 'image',
    ];

    protected function casts(): array
    {
        return [
            'has_emergency' => 'boolean',
            'has_teleconsult' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function zone() { return $this->belongsTo(Zone::class); }
    public function services() { return $this->hasMany(StructureService::class); }
    public function doctors() { return $this->hasMany(DoctorProfile::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
}
