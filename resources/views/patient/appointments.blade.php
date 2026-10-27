@extends('layouts.app')

@section('content')
<h2 class="mb-4">Minhas Consultas</h2>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Médico</th>
                        <th>Especialidade</th>
                        <th>Data/Hora</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>{{ $appointment->doctor->specialty->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($appointment->status === 'scheduled')
                                    <span class="badge bg-success">Agendada</span>
                                @elseif($appointment->status === 'canceled')
                                    <span class="badge bg-danger">Cancelada</span>
                                @else
                                    <span class="badge bg-secondary">{{ $appointment->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($appointment->status === 'scheduled')
                                    <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Deseja realmente cancelar esta consulta?')">Cancelar</button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Você ainda não possui consultas agendadas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection