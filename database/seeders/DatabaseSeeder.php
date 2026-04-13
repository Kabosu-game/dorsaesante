<?php

namespace Database\Seeders;

use App\Models\DoctorProfile;
use App\Models\HealthStructure;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Zones
        $region = Zone::create(['name' => 'Région Capitale', 'code' => 'RC', 'type' => 'region']);
        $district = Zone::create(['name' => 'District Central', 'code' => 'DC', 'type' => 'district', 'parent_id' => $region->id]);
        $commune = Zone::create(['name' => 'Commune Nord', 'code' => 'CN', 'type' => 'commune', 'parent_id' => $district->id]);

        // Admin
        User::create([
            'name'      => 'Admin DORSA',
            'email'     => 'admin@dorsaesante.org',
            'phone'     => '+221000000001',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'zone_id'   => $district->id,
            'is_active' => true,
        ]);

        // Médecin
        $doctor = User::create([
            'name'      => 'Dr. Amina Koné',
            'email'     => 'doctor@dorsaesante.org',
            'phone'     => '+221000000002',
            'password'  => Hash::make('password'),
            'role'      => 'doctor',
            'zone_id'   => $district->id,
            'gender'    => 'female',
            'is_active' => true,
        ]);

        // Patient
        $patient = User::create([
            'name'      => 'Moussa Diallo',
            'email'     => 'patient@dorsaesante.org',
            'phone'     => '+221000000003',
            'password'  => Hash::make('password'),
            'role'      => 'patient',
            'zone_id'   => $commune->id,
            'gender'    => 'male',
            'is_active' => true,
        ]);

        // Structure de santé
        $structure = HealthStructure::create([
            'name'          => 'Hôpital Central DORSA',
            'type'          => 'hopital',
            'description'   => 'Hôpital principal de la région capitale.',
            'address'       => 'Avenue de la Santé, Région Capitale',
            'latitude'      => 12.3456,
            'longitude'     => -1.5678,
            'phone'         => '+221000000100',
            'email'         => 'hopital@dorsaesante.org',
            'zone_id'       => $district->id,
            'has_emergency' => true,
            'has_teleconsult'=> true,
            'opening_hours' => '24h/24',
            'is_active'     => true,
        ]);

        // Profil médecin
        DoctorProfile::create([
            'user_id'              => $doctor->id,
            'specialty'            => 'Médecine Générale',
            'license_number'       => 'MED-2024-001',
            'bio'                  => 'Médecin généraliste avec 10 ans d\'expérience.',
            'health_structure_id'  => $structure->id,
            'consultation_fee'     => 5000,
            'available_teleconsult'=> true,
            'is_verified'          => true,
            'experience_years'     => 10,
            'languages'            => 'fr,bambara',
        ]);

        $this->command->info('Base de données initialisée avec succès !');
        $this->command->info('Admin    : admin@dorsaesante.org / password');
        $this->command->info('Médecin  : doctor@dorsaesante.org / password');
        $this->command->info('Patient  : patient@dorsaesante.org / password');

        $this->call(DorsaDemoContentSeeder::class);
    }
}
