@extends('layouts.app')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold text-primary">Encontre seu Médico e Agende sua Consulta</h1>
        <p class="col-md-8 fs-4 text-muted">Plataforma integrada de agendamentos médicos rápidos e seguros.</p>
        
        <!-- Filtro por Especialidade -->
        <form action="{{ url('/') }}" method="GET" class="row g-3 mt-3">
            <div class="col-md-4">
                <select name="specialty_id" class="form-select">
                    <option value="">Todas as Especialidades</option>
                    @foreach($specialties as $specialty)
                        <option value="{{ $specialty->id }}" {{ ($specialtyId == $specialty->id) ? 'selected' : '' }}>
                            {{ $specialty->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<h2 class="mb-4">Médicos Disponíveis</h2>
<div class="row">
    @forelse($doctors as $doctor)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <span class="badge bg-info text-dark mb-2">{{ $doctor->specialty->name }}</span>
                    <h5 class="card-title fw-bold">{{ $doctor->name }}</h5>
                    <p class="card-text text-muted mb-1"><small>CRM: {{ $doctor->crm }}</small></p>
                    <p class="card-text text-muted mb-3"><small>Atendimento: {{ substr($doctor->work_start_time, 0, 5) }} às {{ substr($doctor->work_end_time, 0, 5) }}</small></p>
                    
                    @auth
                        @if(auth()->user()->role === 'patient')
                            <!-- Botão para abrir modal de agendamento -->
                            <button type="button" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#scheduleModal{{ $doctor->id }}">
                                Agendar Consulta
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm w-100">Faça login para agendar</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Modal de Agendamento -->
        @auth
            @if(auth()->user()->role === 'patient')
                <div class="modal fade" id="scheduleModal{{ $doctor->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('patient.appointments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                                <div class="modal-header">
                                    <h5 class="modal-title">Agendar com {{ $doctor->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Data e Hora da Consulta</label>
                                        <input type="datetime-local" name="appointment_date" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Observações (Opcional)</label>
                                        <textarea name="notes" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">Confirmar Agendamento</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endauth

    @empty
        <div class="col-12">
            <div class="alert alert-warning">Nenhum médico encontrado.</div>
        </div>
    @endforelse
</div>
@endsection