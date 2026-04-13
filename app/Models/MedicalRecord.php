<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'appointment_id', 'type', 'title',
        'content', 'attachments', 'record_date', 'diagnosis', 'medications',
        'follow_up_notes', 'is_confidential',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'medications' => 'array',
            'record_date' => 'date',
            'is_confidential' => 'boolean',
        ];
    }

    public function patient() { return $this->belongsTo(User::class, 'patient_id'); }
    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
}
