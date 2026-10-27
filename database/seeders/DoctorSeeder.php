<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Specialty;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cardio = Specialty::where('name', 'Cardiologia')->first();
        $derma = Specialty::where('name', 'Dermatologia')->first();
        $orto = Specialty::where('name', 'Ortopedia')->first();

        Doctor::create([
            'specialty_id' => $cardio->id,
            'name' => 'Dr. Carlos Silva',
            'crm' => '123456/RJ',
            'phone' => '(24) 99999-1111',
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);

        Doctor::create([
            'specialty_id' => $derma->id,
            'name' => 'Dra. Ana Souza',
            'crm' => '654321/RJ',
            'phone' => '(24) 99999-2222',
            'work_start_time' => '09:00:00',
            'work_end_time' => '18:00:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);

        Doctor::create([
            'specialty_id' => $orto->id,
            'name' => 'Dr. Marcos Oliveira',
            'crm' => '789012/RJ',
            'phone' => '(24) 99999-3333',
            'work_start_time' => '08:00:00',
            'work_end_time' => '12:00:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);

    }
}
