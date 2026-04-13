<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && $this->is_active;
    }

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role',
        'avatar', 'birth_date', 'gender', 'address',
        'zone_id', 'latitude', 'longitude', 'is_active', 'fcm_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isDoctor(): bool { return $this->role === 'doctor'; }
    public function isPatient(): bool { return $this->role === 'patient'; }

    public function doctorProfile() { return $this->hasOne(DoctorProfile::class); }
    public function zone() { return $this->belongsTo(Zone::class); }

    public function appointmentsAsPatient() { return $this->hasMany(Appointment::class, 'patient_id'); }
    public function appointmentsAsDoctor() { return $this->hasMany(Appointment::class, 'doctor_id'); }

    public function teleconsultationsAsPatient() { return $this->hasMany(Teleconsultation::class, 'patient_id'); }
    public function teleconsultationsAsDoctor() { return $this->hasMany(Teleconsultation::class, 'doctor_id'); }

    public function medicalRecords() { return $this->hasMany(MedicalRecord::class, 'patient_id'); }

    public function emergencyProfile() { return $this->hasOne(PatientEmergencyProfile::class); }
    public function communityReports() { return $this->hasMany(CommunityReport::class); }
    public function emergencyAlerts() { return $this->hasMany(EmergencyAlert::class); }
    public function liveStreams() { return $this->hasMany(LiveStream::class, 'doctor_id'); }
    public function appNotifications() { return $this->hasMany(Notification::class); }
}
