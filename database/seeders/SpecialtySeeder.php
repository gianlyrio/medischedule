<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties =[

        ['name' => 'Cardiologia', 'description' => 'Focado no diagnóstico e tratamento de doenças do coração.'],
            ['name' => 'Dermatologia', 'description' => 'Voltado para a saúde da pele, cabelos e unhas.'],
            ['name' => 'Ortopedia', 'description' => 'Tratamento de ossos, músculos, ligamentos e articulações.'],
            ['name' => 'Pediatria', 'description' => 'Assistência médica dedicada a crianças e adolescentes.'],
            ['name' => 'Ginecologia', 'description' => 'Focado na saúde do sistema reprodutor feminino.'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }
    }
}
