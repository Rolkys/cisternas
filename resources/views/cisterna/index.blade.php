
{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicación. --}}
@extends('layouts.app')

@section('content')

@php
    use Carbon\Carbon;
@endphp


<div class="d-flex flex-wrap align-items-center gap-3 mb-3">
    {{-- Título a la izquierda --}}
    <h4 class="mb-0">
        <i class="bi bi-list-ul"></i> Listado de Cisternas
    </h4>

    {{-- Paginación en el centro --}}
    <div class="small mx-auto">
        {{ $cisternas->withQueryString()->links('pagination::bootstrap-4') }}
    </div>

        {{-- Grid de botones con columnas de ancho fijo --}}
        <div class="d-grid gap-2" style="grid-template-columns: repeat(3, 1fr); max-width: 700px;">
            @if(auth()->user()->isRoot() || auth()->user()->isAdmin() || auth()->user()->isUser())
                <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-success w-100" style="padding: 0.5rem 0.75rem; font-size: 0.9rem; white-space: nowrap;">
                    <i class="bi bi-box-arrow-down-left"></i>
                    <span class="d-none d-md-inline ms-1">Importar Excel</span>
                </a>
            @endif
            @if(auth()->user()->isRoot() || auth()->user()->isAdmin() || auth()->user()->isUser())
                <a href="{{ route('cisterna.create') }}" class="btn btn-primary w-100" style="padding: 0.5rem 0.75rem; font-size: 0.9rem; white-space: nowrap;">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-md-inline ms-1">Nueva Cisterna</span>
                </a>
            @endif
            <a href="{{ route('cisterna.export', request()->query()) }}" class="btn btn-outline-primary w-100" style="padding: 0.5rem 0.75rem; font-size: 0.9rem; white-space: nowrap;">
                <i class="bi bi-box-arrow-up-right"></i>
                <span class="d-none d-md-inline ms-1">Exportar Excel</span>
            </a>
            <a href="{{ route('dashboard') }}" 
                class="btn btn-dashboard w-100" 
                style="padding: 0.5rem 0.75rem; font-size: 0.9rem; white-space: nowrap;">
                    <i class="bi bi-speedometer2"></i>
                    <span class="d-none d-md-inline ms-1">Dashboard</span>
            </a>
            <a href="{{ route('planificacion.index') }}" class="btn btn-outline-info w-100" style="padding: 0.5rem 0.75rem; font-size: 0.9rem; white-space: nowrap;">
                <i class="bi bi-calendar2-week"></i>
                <span class="d-none d-md-inline ms-1">Planificación</span>
            </a>
        </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('cisterna.index') }}" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="texto" class="form-control"
                placeholder="Buscar conductor, matrícula cisterna, origen..."
                value="{{ request('texto') }}">
    </div>
    <div class="col-12 col-md-2">
        <input type="date" name="fecha" class="form-control"
                value="{{ request('fecha') }}">
    </div>
    <div class="col-12 col-md-2">
        <input type="number" name="year" class="form-control"
                min="2000" max="2100" step="1" placeholder="Año ej:2026"
                value="{{ request('year') }}">
    </div>
    <div class="col-12 col-md-auto d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">
            <i class="bi bi-search"></i> Filtrar
        </button>
        <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary flex-fill">
            <i class="bi bi-x-lg"></i> Limpiar
        </a>
    </div>
</form>

{{-- Tabla --}}
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle mb-0 table-index-cisternas"
            style="font-size: 0.82rem; white-space: nowrap;">
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
                <th title="Hora estimada consumo Línea 1">H.E.C L1</th>
                <th title="Hora Real Consumo Línea 1">H.R.C L1</th>
                <th title="Hora estimada consumo Línea 2">H.E.C L2</th>
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
                    $hoy = Carbon::now()->startOfDay();
                    $esTamarite = strpos(strtolower((string) ($cisterna->Destino ?? '')), 'tamarite de litera') !== false;
                    if ($esTamarite || $cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2) {
                        $rowClass = 'row-consumida';
                    } elseif ($cisterna->Incidencias) {
                        $rowClass = 'row-incidencia';
                    } elseif ($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isSameDay($hoy)) {
                        $rowClass = 'row-hoy';
                    } elseif ($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isAfter($hoy)) {
                        $rowClass = 'row-futura';
                    } else {
                        $rowClass = 'row-pendiente';
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
                    <td>{{ $cisterna->FechaConsumoMG ? $cisterna->FechaConsumoMG->format('d/m/Y') : '-' }}</td>
                    <td>{{ $cisterna->HoraEstimadaConsumoL1 ? $cisterna->HoraEstimadaConsumoL1->format('H:i') : '--' }}</td>
                    <td>{{ $cisterna->HoraRealConsumoL1 ? $cisterna->HoraRealConsumoL1->format('H:i') : '--' }}</td>
                    <td>{{ $cisterna->HoraEstimadaConsumoL2 ? $cisterna->HoraEstimadaConsumoL2->format('H:i') : '--' }}</td>
                    <td>{{ $cisterna->HoraRealConsumoL2 ? $cisterna->HoraRealConsumoL2->format('H:i') : '--' }}</td>
                    <td>
                        @switch($cisterna->FDA)
                            @case(true)
                                <span class="badge bg-success">Sí</span>
                                @break
                            @case(false)
                                <span class="badge bg-danger">No</span>
                                @break
                            @default
                                <span class="text-muted">—</span>
                        @endswitch
                    </td>
                    <td>
                        @switch($cisterna->GlobalGAP)
                            @case(true)
                                <span class="badge bg-success">Sí</span>
                                @break
                            @case(false)
                                <span class="badge bg-danger">No</span>
                                @break
                            @default
                                <span class="text-muted">—</span>
                        @endswitch
                    </td>
                    <td>
                        @if($esTamarite || $cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2)
                            <span class="badge bg-success">Consumida</span>
                        @elseif($cisterna->Incidencias)
                            <span class="badge bg-danger">Incidencia</span>
                        @elseif($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isSameDay($hoy))
                            <span class="badge bg-info">Hoy</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-consumo"
                                title="Registrar consumo"
                                data-id="{{ $cisterna->IdCisterna }}"
                                data-hec-l1="{{ $cisterna->HoraEstimadaConsumoL1 ? $cisterna->HoraEstimadaConsumoL1->format('H:i') : '' }}"
                                data-hrc-l1="{{ optional($cisterna->HoraRealConsumoL1)->format('H:i') ?? '' }}"
                                data-hec-l2="{{ optional($cisterna->HoraEstimadaConsumoL2)->format('H:i') ?? '' }}"
                                data-hrc-l2="{{ optional($cisterna->HoraRealConsumoL2)->format('H:i') ?? '' }}">
                            <i class="bi bi-clock"></i>
                        </button>
                        <a href="{{ route('cisterna.show', $cisterna->IdCisterna) }}"
                            class="btn btn-sm btn-outline-view" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(!auth()->user()->isOperario())
                            <a href="{{ route('cisterna.edit', $cisterna->IdCisterna) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endif
                        @if(auth()->user()->isRoot() || auth()->user()->isAdmin())
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
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="15" class="text-center text-muted">
                        No hay cisternas registradas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Paginación inferior --}}
<div class="d-flex justify-content-center mt-3">
    {{ $cisternas->withQueryString()->links('pagination::bootstrap-4') }}
</div>

{{-- Leyenda de colores --}}
<div class="mt-3 d-flex flex-wrap gap-3 small text-muted">
    <span><span class="legend-box" style="background:var(--row-consumida)"></span>Consumida</span>
    <span><span class="legend-box" style="background:var(--row-incidencia)"></span>Incidencia</span>
    <span><span class="legend-box" style="background:var(--row-hoy)"></span>Hoy</span>
    <span><span class="legend-box" style="background:var(--row-futura)"></span>Futura</span>
    <span><span class="legend-box" style="background:var(--row-pendiente)"></span>Pendiente</span>
</div>

{{-- Modal consumo --}}
<div class="modal fade" id="modalConsumo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="form-consumo">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clock"></i> Registrar Consumo
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-info small py-2">
                        Solo se puede rellenar <strong>L1</strong> o <strong>L2</strong>, no ambas.
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small">H. Estimada L1 (ref.)</label>
                            <input type="time" id="info-hec-l1"
                                    class="form-control form-control-sm bg-light" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small">H. Estimada L2 (ref.)</label>
                            <input type="time" id="info-hec-l2"
                                    class="form-control form-control-sm bg-light" readonly>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">H. Real Consumo L1</label>
                            <input type="time" name="HoraRealConsumoL1"
                                    id="hrc-l1" class="form-control">
                        </div>  
                        <div class="col-6">
                            <label class="form-label fw-bold">H. Real Consumo L2</label>
                            <input type="time" name="HoraRealConsumoL2"
                                    id="hrc-l2" class="form-control">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * Init event listeners for consumo buttons (open modal).
 */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-consumo').forEach(function (btn) {
        btn.addEventListener('click', function () {
            abrirModal(
                this.dataset.id,
                this.dataset.hecL1,
                this.dataset.hrcL1,
                this.dataset.hecL2,
                this.dataset.hrcL2
            );
        });
    });
});
</script>

@endsection
