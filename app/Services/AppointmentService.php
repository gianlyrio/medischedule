<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class AppointmentService
{
    /**
     * Realiza o agendamento de uma consulta validando as regras de negócio.
     */
    public function schedule(array $data): Appointment
    {
        $appointmentDate = Carbon::parse($data['appointment_date']);

        // 1. Validação de horários retroativos
        if ($appointmentDate->isPast()) {
            throw new Exception('Não é permitido realizar agendamentos para datas ou horários retroativos.');
        }

        $doctorId = $data['doctor_id'];
        $patientId = $data['patient_id'];

        // Buscar duração do slot do médico (padrão 30 minutos)
        $doctor = Doctor::findOrFail($doctorId);
        $durationMinutes = $doctor->slot_duration ?? 30;

        $startTime = $appointmentDate;
        $endTime = (clone $appointmentDate)->addMinutes($durationMinutes);

        // 2. Verifica choque de horários na agenda do médico
        $doctorConflict = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'scheduled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('appointment_date', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime) {
                        $q->where('appointment_date', '<=', $startTime)
                          ->whereRaw("datetime(appointment_date, '+' || ? || ' minutes')", [30])->where('appointment_date', '>', $startTime);
                    });
            })->exists();

        // Verificação simplificada e segura de sobreposição para SQLite/MySQL
        $existingDoctorAppointments = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'scheduled')
            ->get();

        foreach ($existingDoctorAppointments as $existing) {
            $existingStart = Carbon::parse($existing->appointment_date);
            $existingEnd = (clone $existingStart)->addMinutes($existing->doctor->slot_duration ?? 30);

            if ($startTime->lt($existingEnd) && $endTime->gt($existingStart)) {
                throw new Exception('O médico já possui um agendamento conflitante neste horário.');
            }
        }

        // 3. Verifica choque de horários na agenda do paciente
        $existingPatientAppointments = Appointment::where('patient_id', $patientId)
            ->where('status', 'scheduled')
            ->get();

        foreach ($existingPatientAppointments as $existing) {
            $existingStart = Carbon::parse($existing->appointment_date);
            $existingEnd = (clone $existingStart)->addMinutes($existing->doctor->slot_duration ?? 30);

            if ($startTime->lt($existingEnd) && $endTime->gt($existingStart)) {
                throw new Exception('Você já possui outra consulta agendada neste mesmo horário.');
            }
        }

        // 4. Registra a consulta
        return Appointment::create([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appointment_date' => $appointmentDate,
            'status' => 'scheduled',
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Realiza o cancelamento de uma consulta respeitando a regra de 24 horas.
     */
    public function cancel(Appointment $appointment, User $user): bool
    {
        // 1. Valida quem está solicitando o cancelamento
        if ($user->role !== 'admin' && $appointment->patient_id !== $user->id) {
            throw new Exception('Você não tem permissão para cancelar esta consulta.');
        }

        if ($appointment->status === 'canceled') {
            throw new Exception('Esta consulta já está cancelada.');
        }

        // 2. Calcula a diferença de horas entre o momento atual e a data da consulta (regra de 24 horas)
        $now = Carbon::now();
        $appointmentDate = Carbon::parse($appointment->appointment_date);
        
        $hoursDifference = $now->diffInHours($appointmentDate, false);

        if ($hoursDifference < 24 && $user->role !== 'admin') {
            throw new Exception('O cancelamento só pode ser realizado com no mínimo 24 horas de antecedência.');
        }

        // 3. Atualiza o status para canceled
        $appointment->update([
            'status' => 'canceled',
            'canceled_at' => Carbon::now(),
        ]);

        return true;
    }
}