<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Exception;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_nao_permite_agendamento_retroativo()
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'cpf' => '123.456.789-00',
            'phone' => '(24) 98888-8888'
        ]);
        
        $specialty = Specialty::create(['name' => 'Cardiologia', 'description' => 'Teste']);
        
        $doctor = Doctor::create([
            'specialty_id' => $specialty->id,
            'name' => 'Dr. Teste',
            'crm' => '111111/RJ',
            'phone' => '(24) 99999-9999',
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);

        $service = new AppointmentService();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Não é permitido realizar agendamentos para datas ou horários retroativos.');

        $service->schedule([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::now()->subDay(),
        ]);
    }

    public function test_respeita_regra_de_cancelamento_24_horas()
    {
        $patient = User::factory()->create([
            'role' => 'patient',
            'cpf' => '987.654.321-11',
            'phone' => '(24) 97777-7777'
        ]);
        
        $specialty = Specialty::create(['name' => 'Cardiologia', 'description' => 'Teste']);
        
        $doctor = Doctor::create([
            'specialty_id' => $specialty->id,
            'name' => 'Dr. Teste',
            'crm' => '222222/RJ',
            'phone' => '(24) 99999-9999',
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'slot_duration' => 30,
            'is_active' => true,
        ]);

        $service = new AppointmentService();

        // Usa o service para criar o agendamento de forma limpa (daqui a 10 horas)
        $appointment = $service->schedule([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::now()->addHours(10),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('O cancelamento só pode ser realizado com no mínimo 24 horas de antecedência.');

        $service->cancel($appointment, $patient);
    }
}