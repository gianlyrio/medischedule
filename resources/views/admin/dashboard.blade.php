@extends('layouts.app')

@section('content')
<h2 class="mb-4">Painel Administrativo</h2>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body">
                <h5>Total de Médicos</h5>
                <h3>{{ $totalDoctors }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm border-0">
            <div class="card-body">
                <h5>Total de Pacientes</h5>
                <h3>{{ $totalPatients }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white shadow-sm border-0">
            <div class="card-body">
                <h5>Total de Consultas</h5>
                <h3>{{ $totalAppointments }}</h3>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3">Atendimentos de Hoje</h4>
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Horário</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAppointments as $app)
                        <tr>
                            <td>{{ $app->patient->name }}</td>
                            <td>{{ $app->doctor->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($app->appointment_date)->format('H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $app->status === 'scheduled' ? 'success' : 'danger' }}">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Nenhum atendimento agendado para hoje.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection