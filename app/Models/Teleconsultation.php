<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Teleconsultation extends Model
{
    protected $fillable = [
        'appointment_id', 'patient_id', 'doctor_id', 'room_token',
        'status', 'started_at', 'ended_at', 'doctor_notes', 'documents', 'prescription_file',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'documents' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->room_token = $model->room_token ?? Str::uuid();
        });
    }

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient() { return $this->belongsTo(User::class, 'patient_id'); }
    public function doctor() { return $this->belongsTo(User::class, 'doctor_id'); }
}
