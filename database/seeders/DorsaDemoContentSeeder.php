<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\CommunityReport;
use App\Models\EducationalContent;
use App\Models\EmergencyAlert;
use App\Models\HealthAlert;
use App\Models\HealthStructure;
use App\Models\LiveStream;
use App\Models\MentalHealthResource;
use App\Models\Notification;
use App\Models\Teleconsultation;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DorsaDemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $doctor = User::where('role', 'doctor')->first();
        $patient = User::where('role', 'patient')->first();

        if (! $admin || ! $doctor || ! $patient) {
            $this->command->warn('Exécutez d’abord DatabaseSeeder (admin, médecin, patient).');

            return;
        }

        $region = Zone::where('code', 'RC')->first();
        $district = Zone::where('code', 'DC')->first();
        if (! $region || ! $district) {
            $this->command->warn('Zones RC/DC introuvables — exécutez DatabaseSeeder.');

            return;
        }

        $zoneArt = Zone::firstOrCreate(
            ['code' => 'PAU'],
            ['name' => 'Port-au-Prince métropole', 'type' => 'district', 'parent_id' => $region->id]
        );
        $zoneSud = Zone::firstOrCreate(
            ['code' => 'SUD'],
            ['name' => 'Sud — Cayes', 'type' => 'district', 'parent_id' => $region->id]
        );

        HealthStructure::firstOrCreate(
            ['name' => 'Centre de santé communautaire Delmas'],
            [
                'type' => 'centre_sante',
                'description' => 'Consultations, vaccination et urgences mineures.',
                'address' => 'Delmas 33, Port-au-Prince',
                'latitude' => 18.5444,
                'longitude' => -72.3031,
                'phone' => '+50937000001',
                'email' => 'delmas@dorsaesante.org',
                'zone_id' => $zoneArt->id,
                'has_emergency' => true,
                'has_teleconsult' => true,
                'opening_hours' => 'Lun–Ven 8h–16h',
                'is_active' => true,
            ]
        );

        HealthStructure::firstOrCreate(
            ['name' => 'Hôpital Saint-Michel — Les Cayes'],
            [
                'type' => 'hopital',
                'description' => 'Bloc obstétrical, pédiatrie et urgences.',
                'address' => 'Les Cayes',
                'latitude' => 18.2000,
                'longitude' => -73.7500,
                'phone' => '+50937000002',
                'email' => 'cayes@dorsaesante.org',
                'zone_id' => $zoneSud->id,
                'has_emergency' => true,
                'has_teleconsult' => false,
                'opening_hours' => '24h/24',
                'is_active' => true,
            ]
        );

        $eduSeeds = [
            ['title' => 'Prévention du paludisme', 'category' => 'prevention', 'type' => 'article'],
            ['title' => 'Calendrier vaccinal — enfants', 'category' => 'vaccination', 'type' => 'infographic'],
            ['title' => 'Hygiène des mains en milieu communautaire', 'category' => 'hygiene', 'type' => 'article'],
            ['title' => 'Nutrition maternelle et allaitement', 'category' => 'nutrition', 'type' => 'article'],
            ['title' => 'Gestes de premiers secours', 'category' => 'first_aid', 'type' => 'video'],
            ['title' => 'Diabète : signes et suivi', 'category' => 'chronic_diseases', 'type' => 'podcast'],
        ];

        foreach ($eduSeeds as $i => $row) {
            EducationalContent::firstOrCreate(
                ['title' => $row['title']],
                [
                    'content' => 'Contenu éducatif institutionnel Dorsa e-Santé — '.$row['title'].'. '
                        .'Ce module renforce la prévention et l’accès à l’information fiable.',
                    'type' => $row['type'],
                    'category' => $row['category'],
                    'author_id' => $doctor->id,
                    'is_published' => true,
                    'tags' => 'dorsa,sante,prevention',
                    'views_count' => 10 + $i * 5,
                ]
            );
        }

        $mentalSeeds = [
            ['title' => 'Respiration anti-stress (5 minutes)', 'category' => 'stress', 'type' => 'exercise'],
            ['title' => 'Qualité du sommeil : auto-évaluation', 'category' => 'sleep', 'type' => 'quiz'],
            ['title' => 'Comprendre l’anxiété du quotidien', 'category' => 'anxiety', 'type' => 'article'],
            ['title' => 'Prévention de la dépression', 'category' => 'depression', 'type' => 'audio'],
        ];

        foreach ($mentalSeeds as $i => $row) {
            MentalHealthResource::firstOrCreate(
                ['title' => $row['title']],
                [
                    'content' => 'Ressource santé mentale Dorsa e-Santé: '.$row['title'].'.',
                    'type' => $row['type'],
                    'category' => $row['category'],
                    'author_id' => $doctor->id,
                    'requires_professional' => $i % 2 === 0,
                    'is_published' => true,
                    'duration_minutes' => 5 + ($i * 5),
                ]
            );
        }

        HealthAlert::firstOrCreate(
            ['title' => 'Campagne vaccination — semaine nationale'],
            [
                'message' => 'Rendez-vous dans les centres de santé participants. Consultez la liste des structures sur l’application.',
                'level' => 'info',
                'type' => 'vaccination',
                'author_id' => $admin->id,
                'zone_id' => null,
                'is_active' => true,
                'expires_at' => Carbon::now()->addMonths(2),
            ]
        );

        HealthAlert::firstOrCreate(
            ['title' => 'Recommandations saisonnières — hydratation'],
            [
                'message' => 'Période chaude : privilégiez l’eau potable et évitez l’effort intense en plein midi.',
                'level' => 'warning',
                'type' => 'environmental',
                'author_id' => $admin->id,
                'zone_id' => $zoneArt->id,
                'is_active' => true,
                'expires_at' => Carbon::now()->addMonth(),
            ]
        );

        CommunityReport::firstOrCreate(
            ['title' => 'Point d’eau non protégé — quartier Nord'],
            [
                'user_id' => $patient->id,
                'type' => 'hygiene_issue',
                'description' => 'Signalement citoyen pour amélioration de l’hygiène publique. Équipe en suivi.',
                'latitude' => 18.55,
                'longitude' => -72.31,
                'address' => 'Zone Nord, Pétion-Ville',
                'zone_id' => $zoneArt->id,
                'status' => 'under_review',
            ]
        );

        CommunityReport::firstOrCreate(
            ['title' => 'Cas suspect observé — démarche de vigilance'],
            [
                'user_id' => $patient->id,
                'type' => 'suspected_disease',
                'description' => 'Signalement participatif. Les autorités sanitaires sont informées.',
                'latitude' => 18.53,
                'longitude' => -72.29,
                'zone_id' => $zoneArt->id,
                'status' => 'pending',
            ]
        );

        EmergencyAlert::firstOrCreate(
            [
                'user_id' => $patient->id,
                'type' => 'medical',
                'description' => 'Démonstration — alerte test résolue (données de démo).',
            ],
            [
                'latitude' => 18.5944,
                'longitude' => -72.3074,
                'address' => 'Port-au-Prince',
                'status' => 'resolved',
                'resolved_at' => Carbon::now()->subDay(),
            ]
        );

        $structure = HealthStructure::where('name', 'Hôpital Central DORSA')->first()
            ?? HealthStructure::first();

        Appointment::firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_at' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0)->setSecond(0),
            ],
            [
                'health_structure_id' => $structure?->id,
                'duration_minutes' => 30,
                'type' => 'in_person',
                'status' => 'confirmed',
                'reason' => 'Consultation de suivi',
            ]
        );

        $appointmentForTele = Appointment::where('patient_id', $patient->id)->first();
        if ($appointmentForTele) {
            Teleconsultation::firstOrCreate(
                ['room_token' => 'dorsa-room-demo-001'],
                [
                    'appointment_id' => $appointmentForTele->id,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'status' => 'scheduled',
                ]
            );
        }

        Appointment::firstOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_at' => Carbon::now()->addDays(7)->setHour(14)->setMinute(30)->setSecond(0),
            ],
            [
                'health_structure_id' => $structure?->id,
                'duration_minutes' => 45,
                'type' => 'teleconsultation',
                'status' => 'pending',
                'reason' => 'Téléconsultation — avis médical',
            ]
        );

        LiveStream::firstOrCreate(
            ['stream_key' => 'dorsa-demo-live-sante-publique'],
            [
                'doctor_id' => $doctor->id,
                'title' => 'Live — Prévention et vaccination',
                'description' => 'Session interactive avec un professionnel de santé (démo).',
                'stream_url' => 'https://example.org/stream/dorsa-demo',
                'topic' => 'sante_publique',
                'status' => 'scheduled',
                'scheduled_at' => Carbon::now()->addDays(2),
                'viewers_count' => 0,
            ]
        );

        LiveStream::firstOrCreate(
            ['stream_key' => 'dorsa-demo-live-mental-health'],
            [
                'doctor_id' => $doctor->id,
                'title' => 'Live — Bien-être psychologique',
                'description' => 'Espace d’écoute et ressources (démo).',
                'topic' => 'sante_mentale',
                'status' => 'live',
                'started_at' => Carbon::now()->subMinutes(15),
                'viewers_count' => 42,
            ]
        );

        Notification::firstOrCreate(
            ['user_id' => $patient->id, 'title' => 'Rappel rendez-vous'],
            [
                'body' => 'Votre rendez-vous est confirmé pour cette semaine.',
                'type' => 'appointment',
                'is_read' => false,
            ]
        );
        Notification::firstOrCreate(
            ['user_id' => $patient->id, 'title' => 'Alerte sanitaire active'],
            [
                'body' => 'Nouvelle alerte dans votre zone.',
                'type' => 'health_alert',
                'is_read' => false,
            ]
        );

        $this->command->info('Contenu démo Dorsa : éducation, santé mentale, alertes, signalements, urgences, RDV, téléconsultations, lives, notifications, structures, zones.');
    }
}
