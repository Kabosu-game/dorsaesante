<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityReportController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\EducationalContentController;
use App\Http\Controllers\Api\EmergencyController;
use App\Http\Controllers\Api\HealthAlertController;
use App\Http\Controllers\Api\HealthStructureController;
use App\Http\Controllers\Api\LiveStreamController;
use App\Http\Controllers\Api\MedicalRecordController;
use App\Http\Controllers\Api\MentalHealthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PatientEmergencyProfileController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\TeleconsultationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dorsa e-Santé — Routes API
|--------------------------------------------------------------------------
*/

// ── Routes publiques ────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Contenus publics
Route::get('/health-structures',          [HealthStructureController::class, 'index']);
Route::get('/health-structures/{healthStructure}', [HealthStructureController::class, 'show']);
Route::get('/health-alerts',              [HealthAlertController::class, 'index']);
Route::get('/health-alerts/{healthAlert}',[HealthAlertController::class, 'show']);
Route::get('/education',                  [EducationalContentController::class, 'index']);
Route::get('/education/{educationalContent}', [EducationalContentController::class, 'show']);
Route::get('/mental-health',              [MentalHealthController::class, 'index']);
Route::get('/mental-health/{mentalHealthResource}', [MentalHealthController::class, 'show']);
Route::get('/doctors',                    [DoctorController::class, 'index']);
Route::get('/doctors/{doctor}',           [DoctorController::class, 'show']);
Route::get('/doctors/{doctor}/availability', [DoctorController::class, 'availability']);
Route::get('/zones',                      [ZoneController::class, 'index']);
Route::get('/live-streams',               [LiveStreamController::class, 'index']);
Route::get('/live-streams/{liveStream}',  [LiveStreamController::class, 'show']);
Route::get('/emergency/nearby',           [EmergencyController::class, 'nearbyStructures']);

// ── Routes authentifiées ─────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/me',              [AuthController::class, 'me']);
        Route::put('/profile',         [AuthController::class, 'updateProfile']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout',         [AuthController::class, 'logout']);
    });

    // Utilisateurs (admin)
    Route::prefix('users')->group(function () {
        Route::get('/',                [UserController::class, 'index']);
        Route::get('/{user}',          [UserController::class, 'show']);
        Route::patch('/{user}/toggle', [UserController::class, 'toggleActive']);
    });

    // Médecins
    Route::put('/doctors/profile', [DoctorController::class, 'updateProfile']);

    // Structures de santé (admin)
    Route::post('/health-structures',             [HealthStructureController::class, 'store']);
    Route::put('/health-structures/{healthStructure}',    [HealthStructureController::class, 'update']);
    Route::delete('/health-structures/{healthStructure}', [HealthStructureController::class, 'destroy']);

    // Rendez-vous
    Route::prefix('appointments')->group(function () {
        Route::get('/',                         [AppointmentController::class, 'index']);
        Route::post('/',                        [AppointmentController::class, 'store']);
        Route::get('/{appointment}',            [AppointmentController::class, 'show']);
        Route::patch('/{appointment}/status',   [AppointmentController::class, 'updateStatus']);
        Route::patch('/{appointment}/cancel',   [AppointmentController::class, 'cancel']);
    });

    // Téléconsultations
    Route::prefix('teleconsultations')->group(function () {
        Route::get('/',                                [TeleconsultationController::class, 'index']);
        Route::post('/',                               [TeleconsultationController::class, 'store']);
        Route::get('/{teleconsultation}',              [TeleconsultationController::class, 'show']);
        Route::patch('/{teleconsultation}/start',      [TeleconsultationController::class, 'start']);
        Route::patch('/{teleconsultation}/end',        [TeleconsultationController::class, 'end']);
    });

    // Dossiers médicaux
    Route::prefix('medical-records')->group(function () {
        Route::get('/',                    [MedicalRecordController::class, 'index']);
        Route::post('/',                   [MedicalRecordController::class, 'store']);
        Route::get('/{medicalRecord}',     [MedicalRecordController::class, 'show']);
        Route::get('/patient/{patientId}', [MedicalRecordController::class, 'patientRecords']);
    });

    // Fiche urgence / premiers secours (patient édite, admin & médecin consultent)
    Route::get('/emergency-medical-sheet/me', [PatientEmergencyProfileController::class, 'mine']);
    Route::put('/emergency-medical-sheet/me', [PatientEmergencyProfileController::class, 'updateMine']);
    Route::get('/emergency-medical-sheet/patient/{user}', [PatientEmergencyProfileController::class, 'forPatient']);

    // Santé mentale
    Route::post('/mental-health',         [MentalHealthController::class, 'store']);

    // Contenu éducatif
    Route::post('/education',             [EducationalContentController::class, 'store']);
    Route::put('/education/{educationalContent}', [EducationalContentController::class, 'update']);

    // Alertes sanitaires
    Route::post('/health-alerts',                    [HealthAlertController::class, 'store']);
    Route::patch('/health-alerts/{healthAlert}/deactivate', [HealthAlertController::class, 'deactivate']);

    // Signalements communautaires
    Route::prefix('community-reports')->group(function () {
        Route::get('/',                                [CommunityReportController::class, 'index']);
        Route::post('/',                               [CommunityReportController::class, 'store']);
        Route::get('/{communityReport}',               [CommunityReportController::class, 'show']);
        Route::patch('/{communityReport}/respond',     [CommunityReportController::class, 'respond']);
    });

    // Urgences
    Route::prefix('emergency')->group(function () {
        Route::post('/alert',                       [EmergencyController::class, 'sendAlert']);
        Route::get('/my-alerts',                    [EmergencyController::class, 'myAlerts']);
        Route::patch('/{emergencyAlert}/status',    [EmergencyController::class, 'updateStatus']);
    });

    // Lives médicaux
    Route::prefix('live-streams')->group(function () {
        Route::post('/',                                [LiveStreamController::class, 'store']);
        Route::patch('/{liveStream}/go-live',           [LiveStreamController::class, 'goLive']);
        Route::patch('/{liveStream}/end',               [LiveStreamController::class, 'endLive']);
        Route::patch('/{liveStream}/viewers',           [LiveStreamController::class, 'updateViewers']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/',                             [NotificationController::class, 'index']);
        Route::get('/unread-count',                 [NotificationController::class, 'unreadCount']);
        Route::patch('/read-all',                   [NotificationController::class, 'markAllAsRead']);
        Route::patch('/{notification}/read',        [NotificationController::class, 'markAsRead']);
    });

    // Zones (admin)
    Route::post('/zones', [ZoneController::class, 'store']);

    // Statistiques
    Route::prefix('stats')->group(function () {
        Route::get('/dashboard',      [StatisticsController::class, 'dashboard']);
        Route::get('/by-zone',        [StatisticsController::class, 'appointmentsByZone']);
        Route::get('/top-diseases',   [StatisticsController::class, 'topDiseases']);
        Route::get('/me',             [StatisticsController::class, 'myStats']);
    });
});
