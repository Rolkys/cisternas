@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-list-ul"></i> Listado de Cisternas</h4>
    <div class="d-flex align-items-center gap-2">
        <div class="small">
            {{ $cisternas->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
        <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Importar Excel
        </a>
        <a href="{{ route('cisterna.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nueva Cisterna
        </a>
        <a href="{{ route('cisterna.export', request()->query()) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-download"></i> Exportar Excel
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('planificacion.index') }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-calendar2-week"></i> Planificación
        </a>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('cisterna.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="texto" class="form-control"
                placeholder="Buscar conductor, matrícula, origen..."
                value="{{ request('texto') }}">
    </div>
    <div class="col-md-3">
        <input type="date" name="fecha" class="form-control"
                value="{{ request('fecha') }}">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Filtrar
        </button>
        <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg"></i> Limpiar
        </a>
    </div>
</form>

{{-- Tabla --}}
<div>
    <table class="table table-bordered table-hover align-middle" style="font-size: 0.82rem; white-space: nowrap;">
        <thead>
            <tr>
                <th>OF</th>
                <th>Nº</th>
                <th>Origen</th>
                <th>Destino</th>
                <th title="Matrícula Camión">Matrícula.T</th>
                <th title="Matrícula Cisterna">Matrícula.C</th>
                <th>Conductor</th>
                <th>Teléfono</th>
                <th title="Fecha Consumo MG">Fecha Consumo</th>
                <th title="Hora Estimada Consumo Línea 1">H.E.C L1</th>
                <th title="Hora Estimada Consumo Línea 2">H.E.C L2</th>
                <th title="Hora Real Consumo Línea 1">H.R.C L1</th>
                <th title="Hora Real Consumo Línea 2">H.R.C L2</th>
                <th title="Food and Drug Administration">FDA</th>
                <th title="GlobalGAP">GAP</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cisternas as $cisterna)

                @php
                    $hoy = now()->startOfDay();
                    $rowClass = '';
                    if ($cisterna->Incidencias) {
                        $rowClass = 'row-incidencia';  // rojo
                    } elseif ($cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2) {
                        $rowClass = 'row-consumida';   // verde
                    } elseif ($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isSameDay($hoy)) {
                        $rowClass = 'row-hoy';         // azul
                    } elseif ($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isAfter($hoy)) {
                        $rowClass = 'row-futura';      // amarillo
                    } else {
                        $rowClass = 'row-pendiente';   // gris
                    }
                @endphp

                <tr class="{{ $rowClass }}">
                    <td>{{ $cisterna->OF }}</td>
                    <td>{{ str_pad($cisterna->NumeroCisterna, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $cisterna->Origen ?: '—' }}</td>
                    <td>{{ $cisterna->Destino ?: '—' }}</td>
                    <td>{{ $cisterna->Matricula ?: '—' }}</td>
                    <td>{{ $cisterna->MatriculaCisterna ?: '—' }}</td>
                    <td>{{ $cisterna->Conductor }}</td>
                    <td>{{ $cisterna->Telefono ?: '—' }}</td>
                    <td>{{ $cisterna->FechaConsumoMG?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraEstimadaConsumoL1?->format('H:i') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraEstimadaConsumoL2?->format('H:i') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraRealConsumoL1?->format('H:i') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraRealConsumoL2?->format('H:i') ?? '—' }}</td>
                    <td>
                        @if($cisterna->FDA === true)
                            <span class="badge bg-success">Sí</span>
                        @elseif($cisterna->FDA === false)
                            <span class="badge bg-danger">No</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($cisterna->GlobalGAP === true)
                            <span class="badge bg-success">Sí</span>
                        @elseif($cisterna->GlobalGAP === false)
                            <span class="badge bg-danger">No</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($cisterna->Incidencias)
                            <span class="badge bg-danger">Incidencia</span>
                        @elseif($cisterna->HoraRealConsumoL1)
                            <span class="badge bg-success">Consumida</span>
                        @elseif($cisterna->FechaConsumoMG?->isSameDay($hoy))
                            <span class="badge bg-info">Hoy</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        {{-- Botón modal consumo --}}
                        <button class="btn btn-sm btn-outline-warning"
                                title="Registrar consumo"
                                onclick="abrirModal(
                                    {{ $cisterna->IdCisterna }},
                                    '{{ $cisterna->HoraEstimadaConsumoL1?->format('H:i') ?? '' }}',
                                    '{{ $cisterna->HoraEstimadaConsumoL2?->format('H:i') ?? '' }}',
                                    '{{ $cisterna->HoraRealConsumoL1?->format('H:i') ?? '' }}',
                                    '{{ $cisterna->HoraRealConsumoL2?->format('H:i') ?? '' }}',
                                    '{{ addslashes($cisterna->Observaciones ?? '') }}'
                                )">
                            <i class="bi bi-clock"></i>
                        </button>
                        <a href="{{ route('cisterna.show', $cisterna->IdCisterna) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('cisterna.edit', $cisterna->IdCisterna) }}"
                            class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST"
                                action="{{ route('cisterna.destroy', $cisterna->IdCisterna) }}"
                                style="display:inline"
                                onsubmit="return confirm('¿Eliminar esta cisterna?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="text-center text-muted">No hay cisternas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal consumo --}}
<div class="modal fade" id="modalConsumo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="form-consumo">
                @csrf
                @method('PATCH')
                <div class="modal-header" style="background:#0f2130; color:#fff;">
                    <h5 class="modal-title"><i class="bi bi-clock"></i> Registrar Consumo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-info small py-2">
                        Solo se puede rellenar <strong>L1</strong> o <strong>L2</strong>, no ambas.
                    </div>

                    {{-- HEC informativo --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">H. Estimada L1</label>
                            <input type="time" id="info-hec-l1" class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">H. Estimada L2</label>
                            <input type="time" id="info-hec-l2" class="form-control form-control-sm bg-light" readonly>
                        </div>
                    </div>

                    <hr>

                    {{-- HRC --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">H. Real Consumo L1</label>
                            <input type="time" name="HoraRealConsumoL1" id="hrc-l1" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">H. Real Consumo L2</label>
                            <input type="time" name="HoraRealConsumoL2" id="hrc-l2" class="form-control">
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea name="Observaciones" id="modal-obs" class="form-control" rows="3"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModal(id, hecL1, hecL2, hrcL1, hrcL2, obs) {
    document.getElementById('form-consumo').action = `/cisterna/${id}/consumo`;
    document.getElementById('info-hec-l1').value = hecL1;
    document.getElementById('info-hec-l2').value = hecL2;
    document.getElementById('hrc-l1').value = hrcL1;
    document.getElementById('hrc-l2').value = hrcL2;
    document.getElementById('modal-obs').value = obs;

    // Reset estado
    document.getElementById('hrc-l1').disabled = false;
    document.getElementById('hrc-l2').disabled = false;

    // Bloquear según valor inicial
    if (hrcL1) {
        document.getElementById('hrc-l2').disabled = true;
    } else if (hrcL2) {
        document.getElementById('hrc-l1').disabled = true;
    }

    new bootstrap.Modal(document.getElementById('modalConsumo')).show();
}

// Exclusividad L1/L2
document.getElementById('hrc-l1').addEventListener('input', function() {
    const l2 = document.getElementById('hrc-l2');
    if (this.value) {
        l2.value = '';
        l2.disabled = true;
    } else {
        l2.disabled = false;
    }
});

document.getElementById('hrc-l2').addEventListener('input', function() {
    const l1 = document.getElementById('hrc-l1');
    if (this.value) {
        l1.value = '';
        l1.disabled = true;
    } else {
        l1.disabled = false;
    }
});
</script>

@endsection