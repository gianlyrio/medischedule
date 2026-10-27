@extends('layouts.app')

@section('content')
<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold text-primary">Bem-vindo, {{ auth()->user()->name }}!</h1>
        <p class="col-md-8 fs-4 text-muted">Aqui você pode visualizar médicos disponíveis e gerenciar suas consultas.</p>
        <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary mt-2">Ver Minhas Consultas</a>
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
                    
                    <!-- Botão para abrir modal de agendamento -->
                    <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#scheduleModal{{ $doctor->id }}">
                        Agendar Consulta
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal de Agendamento -->
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
    @empty
        <div class="col-12">
            <div class="alert alert-warning">Nenhum médico encontrado.</div>
        </div>
    @endforelse
</div>
@endsection