<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientEmergencyProfile extends Model
{
    protected $fillable = [
        'user_id',
        'blood_group',
        'allergies',
        'chronic_conditions',
        'regular_medications',
        'emergency_contact_name',
        'emergency_contact_phone',
        'first_aid_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
