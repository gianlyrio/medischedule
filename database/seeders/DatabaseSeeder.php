<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
Use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
    // Criar Administrador padrão
        User::create([
            'name' => 'Administrador MediSchedule',
            'email' => 'admin@medischedule.test',
            'password' => Hash::make('password'),
            'cpf' => '000.000.000-00',
            'phone' => '(24) 98888-0000',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Criar Paciente de teste
        User::create([
            'name' => 'Paciente Teste',
            'email' => 'paciente@medischedule.test',
            'password' => Hash::make('password'),
            'cpf' => '111.111.111-11',
            'phone' => '(24) 97777-1111',
            'role' => 'patient',
            'status' => 'active',
        ]);

        $this->call([
            SpecialtySeeder::class,
            DoctorSeeder::class,
        ]);
    }
}
