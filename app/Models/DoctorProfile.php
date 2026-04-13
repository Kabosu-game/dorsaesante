<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id', 'specialty', 'license_number', 'diploma', 'bio',
        'structure_name', 'health_structure_id', 'consultation_fee',
        'available_teleconsult', 'is_verified', 'experience_years', 'languages',
    ];

    protected function casts(): array
    {
        return [
            'available_teleconsult' => 'boolean',
            'is_verified' => 'boolean',
            'consultation_fee' => 'decimal:2',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function healthStructure() { return $this->belongsTo(HealthStructure::class); }
}
