
{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Dashboard</h4>
    <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-list-ul"></i> Ver listado
    </a>
</div>
{{-- Filtro por rango de fechas --}}
<form method="GET" action="{{ route('dashboard') }}" class="row g-2 mb-4 align-items-end">
    <div class="col-auto">
        <label class="form-label small mb-1">Desde</label>
        <input type="date" name="desde" class="form-control-sm"
                value="{{ $desde }}">
    </div>
    <div class="col-auto">
        <label class="form-label small mb-1">Hasta</label>
        <input type="date" name="hasta" class="form-control-sm"
                value="{{ $hasta }}">
    </div>
    <div class="col-auto d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-funnel"></i> Filtrar
        </button>
        <a href="{{ route('dashboard') }}" class="btn-outline-secondary btn-sm">
            <i class="bi bi-x-lg"></i> Limpiar
        </a>
    </div>
    @if($desde||$hasta)
        <div class="col-auto">
            <span class="badge bg-info">
                Filtrado: {{ $desde ?? '-' }} → {{ $hasta ?? '-' }}
            </span>
        </div>
    @endif
</form>

{{-- Tarjetas de métricas --}}
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card text-center shadow-sm h-100">
            <div class="card-body">
                <div class="fs-1 fw-bold text-primary">{{ $total }}</div>
                <div class="text-muted small">Total cisternas</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center shadow-sm h-100" style="border-left: 4px solid #90D5FF;">
            <div class="card-body">
                <div class="fs-1 fw-bold" style="color:#0d6efd;">{{ $hoy_count }}</div>
                <div class="text-muted small">Programadas hoy</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center shadow-sm h-100" style="border-left: 4px solid #adebb3;">
            <div class="card-body">
                <div class="fs-1 fw-bold text-success">{{ $consumidas }}</div>
                <div class="text-muted small">Consumidas</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center shadow-sm h-100" style="border-left: 4px solid #FFEE8C;">
            <div class="card-body">
                <div class="fs-1 fw-bold text-warning">{{ $pendientes }}</div>
                <div class="text-muted small">Pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center shadow-sm h-100" style="border-left: 4px solid #FF746C;">
            <div class="card-body">
                <div class="fs-1 fw-bold text-danger">{{ $incidencias }}</div>
                <div class="text-muted small">Con incidencias</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Cisternas programadas para hoy --}}
    <div class="col-md-7">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold" style="background-color: #e10734 !important; color: #ffffff !important; border-color: #e10734 !important;">
                <i class="bi bi-calendar-check"></i> Programadas para hoy
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead style="background:#0f2130; color:#fff;">
                        <tr>
                            <th>OF</th>
                            <th>Nº</th>
                            <th>Conductor</th>
                            <th>H. Est. L1</th>
                            <th>H. Est. L2</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hoy_cisternas as $c)
                            @php
                                $esTamariteHoy = str_contains(strtolower((string) ($c->Destino ?? '')), 'tamarite de litera');
                            @endphp
                            <tr class="{{ ($esTamariteHoy || $c->HoraRealConsumoL1 || $c->HoraRealConsumoL2) ? 'row-consumida' : ($c->Incidencias ? 'row-incidencia' : 'row-hoy') }}">
                                <td>{{ $c->OF }}</td>
                                <td>{{ str_pad($c->NumeroCisterna, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $c->Conductor }}</td>
                                <td>{{ optional($c->HoraEstimadaConsumoL1)->format('H:i') ?? '—' }}</td>
                                <td>{{ optional($c->HoraEstimadaConsumoL2)->format('H:i') ?? '—' }}</td>
                                <td>
                                    @if($esTamariteHoy || $c->HoraRealConsumoL1 || $c->HoraRealConsumoL2)
                                        <span class="badge bg-success">Consumida</span>
                                    @elseif($c->Incidencias)
                                        <span class="badge bg-danger">Incidencia</span>
                                    @else
                                        <span class="badge bg-info">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    No hay cisternas programadas para hoy.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Últimas añadidas --}}
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-bold" style="background-color: #e10734 !important; color: #ffffff !important; border-color: #e10734 !important;">
                <i class="bi bi-clock-history"></i> Últimas añadidas
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead style="background:#0f2130; color:#fff;">
                        <tr>
                            <th>OF</th>
                            <th>Conductor</th>
                            <th>Origen</th>
                            <th>Destino</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recientes as $c)
                            <tr>
                                <td>{{ $c->OF }}</td>
                                <td>{{ $c->Conductor }}</td>
                                <td>{{ $c->Origen ?: '—' }}</td>
                                <td>{{ $c->Destino ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Calendario de años --}}
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header fw-bold" style="background-color: #e10734 !important; color: #ffffff !important; border-color: #e10734 !important;">
                    <i class="bi bi-calendar3"></i> Cisternas por año
                </div>
                <div class="card-body">
                    {{-- Botones de años --}}
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @forelse($años as $año)
                            <a href="{{ route('dashboard', ['año' => $año]) }}"
                                class="btn {{ $añoSeleccionado == $año ? 'btn-primary' : 'btn-outline-primary' }}">
                                {{ $año }}
                            </a>
                        @empty
                            <span class="text-muted">No hay años disponibles.</span>
                        @endforelse
                    </div>

                    {{-- Listado del año seleccionado --}}
                    @if($añoSeleccionado)
                        <h6 class="fw-bold mb-3">
                            Cisternas de {{ $añoSeleccionado }}
                            <span class="badge bg-secondary">{{ $cisternasDelAño->count() }}</span>
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover align-middle" style="font-size:0.82rem; white-space:nowrap;">
                                <thead style="background:#0f2130; color:#fff;">
                                    <tr>
                                        <th>OF</th>
                                        <th>Nº</th>
                                        <th>Conductor</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th>Fecha Consumo</th>
                                        <th>H.R.C L1</th>
                                        <th>H.R.C L2</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cisternasDelAño as $cisterna)
                                        @php
                                            $hoy = now()->startOfDay();
                                            $esTamarite = str_contains(strtolower((string) ($cisterna->Destino ?? '')), 'tamarite de litera');
                                            $rowClass = '';
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
                                            <td>{{ $cisterna->Conductor }}</td>
                                            <td>{{ $cisterna->Origen ?: '—' }}</td>
                                            <td>{{ $cisterna->Destino ?: '—' }}</td>
                                            <td>{{ optional($cisterna->FechaConsumoMG)->format('d/m/Y') ?? '—' }}</td>
                                            <td>{{ optional($cisterna->HoraRealConsumoL1)->format('H:i') ?? '—' }}</td>
                                            <td>{{ optional($cisterna->HoraRealConsumoL2)->format('H:i') ?? '—' }}</td>
                                            <td>
                                                @if($esTamarite || $cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2)
                                                    <span class="badge bg-success">Consumida</span>
                                                @elseif($cisterna->Incidencias)
                                                    <span class="badge bg-danger">Incidencia</span>
                                                @elseif(optional($cisterna->FechaConsumoMG)->isSameDay($hoy))
                                                    <span class="badge bg-info">Hoy</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                No hay cisternas en {{ $añoSeleccionado }}.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Selecciona un año para ver sus cisternas.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection

