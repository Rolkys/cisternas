{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0"><i class="bi bi-list-ul"></i> Listado de Cisternas</h4>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="small">
            {{ $cisternas->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
        @if(auth()->user()->isRoot() || auth()->user()->isAdmin() || auth()->user()->isUser())
            <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i>
                <span class="d-none d-md-inline">Importar Excel</span>
            </a>
        @endif
        @if(auth()->user()->isRoot() || auth()->user()->isAdmin() || auth()->user()->isUser())
            <a href="{{ route('cisterna.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-md-inline">Nueva Cisterna</span>
            </a>
        @endif
        <a href="{{ route('cisterna.export', request()->query()) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-download"></i>
            <span class="d-none d-md-inline">Exportar Excel</span>
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-speedometer2"></i>
            <span class="d-none d-md-inline">Dashboard</span>
        </a>
        <a href="{{ route('planificacion.index') }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-calendar2-week"></i>
            <span class="d-none d-md-inline">Planificación</span>
        </a>
        {{-- En la sección de botones superiores, después del botón de Planificación --}}
        @if(auth()->user()->isRoot() || auth()->user()->isAdmin())
            <form method="POST" 
                action="{{ route('cisterna.destroyAll') }}" 
                style="display:inline"
                onsubmit="return confirm('¿Eliminar TODAS las cisternas? Esta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3"></i>
                    <span class="d-none d-md-inline">Eliminar Todas</span>
                </button>
            </form>
            {{-- TODO: Eliminar este botón después de la migración o cuando ya no sea necesario --}}
        @endif
        
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('cisterna.index') }}" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="texto" class="form-control"
                placeholder="Buscar conductor, matrícula cisterna, origen..."
                value="{{ request('texto') }}">
    </div>
    <div class="col-12 col-md-3">
        <input type="date" name="fecha" class="form-control"
                value="{{ request('fecha') }}">
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
    <table class="table table-bordered table-hover align-middle mb-0"
            style="font-size: 0.82rem; white-space: nowrap;">
        <thead>
                <th>OF</th>
                <th>Nº</th>
                <th>Origen</th>
                <th>Destino</th>
                <th title="Matrícula Camión">Matrícula.T</th>
                <th title="Matrícula Cisterna">Matrícula.C</th>
                <th>Conductor</th>
                <th>Teléfono</th>
                <th title="Fecha Consumo MG">Fecha Consumo</th>
                <th title="Hora estida consumo Línea 1">H.E.C L1</th>
                <th title="Hora Real Consumo Línea 1">H.R.C L1</th>
                <th title="Hora estida consumo Línea 2">H.E.C L2</th>
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
                    if ($cisterna->Incidencias) {
                        $rowClass = 'row-incidencia';
                    } elseif ($cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2) {
                        $rowClass = 'row-consumida';
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
                    <td>{{ $cisterna->FechaConsumoMG?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraEstimadaConsumoL1?->format('H:i') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraRealConsumoL1?->format('H:i') ?? '—' }}</td>
                    <td>{{ $cisterna->HoraEstimadaConsumoL2?->format('H:i') ?? '—' }}</td>
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
                        @elseif($cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2)
                            <span class="badge bg-success">Consumida</span>
                        @elseif($cisterna->FechaConsumoMG?->isSameDay($hoy))
                            <span class="badge bg-info">Hoy</span>
                        @else
                            <span class="badge bg-warning text-dark">Pendiente</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-warning btn-consumo"
                                title="Registrar consumo"
                                data-id="{{ $cisterna->IdCisterna }}"
                                data-hec-l1="{{ $cisterna->HoraEstimadaConsumoL1?->format('H:i') ?? '' }}"
                                data-hrc-l1="{{ $cisterna->HoraRealConsumoL1?->format('H:i') ?? '' }}"
                                data-hec-l2="{{ $cisterna->HoraEstimadaConsumoL2?->format('H:i') ?? '' }}"
                                data-hrc-l2="{{ $cisterna->HoraRealConsumoL2?->format('H:i') ?? '' }}">
                            <i class="bi bi-clock"></i>
                        </button>
                        <a href="{{ route('cisterna.show', $cisterna->IdCisterna) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if(!auth()->user()->isOperario())
                            <a href="{{ route('cisterna.edit', $cisterna->IdCisterna) }}"
                                class="btn btn-sm btn-outline-primary" title="Editar">
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
