@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-eye"></i> Detalle Cisterna — OF {{ $cisterna->OF }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('cisterna.edit', $cisterna->IdCisterna) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3">

    {{-- Datos generales --}}
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">Datos generales</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>OF</th><td>{{ $cisterna->OF }}</td></tr>
                    <tr><th>Nº Cisterna</th><td>{{ str_pad($cisterna->NumeroCisterna, 4, '0', STR_PAD_LEFT) }}</td></tr>
                    <tr><th>Conductor</th><td>{{ $cisterna->Conductor }}</td></tr>
                    <tr><th>Teléfono</th><td>{{ $cisterna->Telefono ?: '—' }}</td></tr>
                    <tr><th>Transporte</th><td>{{ $cisterna->Transporte ?: '—' }}</td></tr>
                    <tr><th>Origen</th><td>{{ $cisterna->Origen ?: '—' }}</td></tr>
                    <tr><th>Destino</th><td>{{ $cisterna->Destino ?: '—' }}</td></tr>
                    <tr><th>Matrícula Camión</th><td>{{ $cisterna->Matricula ?: '—' }}</td></tr>
                    <tr><th>Matrícula Cisterna</th><td>{{ $cisterna->MatriculaCisterna ?: '—' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Fechas y horas --}}
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">Fechas y horas</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>Hora Salida</th><td>{{ $cisterna->HoraSalida?->format('d/m/Y H:i') ?? '—' }}</td></tr>
                    <tr><th>Fecha Entrada MG</th><td>{{ $cisterna->FechaEntradaMG?->format('d/m/Y H:i') ?? '—' }}</td></tr>
                    <tr><th>Hora Llegada Estimada</th><td>{{ $cisterna->HoraLlegadaEstimada?->format('d/m/Y H:i') ?? '—' }}</td></tr>
                    <tr><th>Fecha Consumo MG</th><td>{{ $cisterna->FechaConsumoMG?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><th>Hora Estimada Consumo L1</th><td>{{ $cisterna->HoraEstimadaConsumoL1?->format('H:i') ?? '—' }}</td></tr>
                    <tr><th>Hora Estimada Consumo L2</th><td>{{ $cisterna->HoraEstimadaConsumoL2?->format('H:i') ?? '—' }}</td></tr>
                    <tr><th>Hora Real Consumo L1</th><td>{{ $cisterna->HoraRealConsumoL1?->format('H:i') ?? '—' }}</td></tr>
                    <tr><th>Hora Real Consumo L2</th><td>{{ $cisterna->HoraRealConsumoL2?->format('H:i') ?? '—' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Certificaciones --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">Certificaciones</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <th>GlobalGAP</th>
                        <td>
                            @if($cisterna->GlobalGAP === true)
                                <span class="badge bg-success">Sí</span>
                            @elseif($cisterna->GlobalGAP === false)
                                <span class="badge bg-danger">No</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>FDA</th>
                        <td>
                            @if($cisterna->FDA === true)
                                <span class="badge bg-success">Sí</span>
                            @elseif($cisterna->FDA === false)
                                <span class="badge bg-danger">No</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Observaciones --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold">Observaciones</div>
            <div class="card-body">
                <p class="mb-0">{{ $cisterna->Observaciones ?: '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Incidencias --}}
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold text-danger">
                <i class="bi bi-exclamation-triangle"></i> Incidencias
            </div>
            <div class="card-body">
                <p class="mb-0 {{ $cisterna->Incidencias ? 'text-danger' : 'text-muted' }}">
                    {{ $cisterna->Incidencias ?: 'Sin incidencias' }}
                </p>
            </div>
        </div>
    </div>

</div>

{{-- Eliminar --}}
<div class="mt-4">
    <form method="POST" action="{{ route('cisterna.destroy', $cisterna->IdCisterna) }}"
            onsubmit="return confirm('¿Seguro que quieres eliminar esta cisterna?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash"></i> Eliminar cisterna
        </button>
    </form>
</div>

@endsection