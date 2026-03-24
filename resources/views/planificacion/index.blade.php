@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-calendar2-week"></i> Planificación</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <a href="{{ route('planificacion.exportar') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
        </a>
        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('planificacion.clear') }}"
                  onsubmit="return confirm('¿Limpiar toda la planificación?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Limpiar todo
                </button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Formulario añadir — solo Admin/Root --}}
@if(auth()->user()->isAdmin())
<div class="card shadow-sm mb-4">
    <div class="card-header fw-bold">Nueva fila</div>
    <div class="card-body">
        <form method="POST" action="{{ route('planificacion.store') }}">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Nº Cisterna</label>
                    <input type="number" name="NumeroCisterna" class="form-control"
                           value="{{ old('NumeroCisterna') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Destino</label>
                    <input type="text" name="Destino" class="form-control"
                           value="{{ old('Destino') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Consumo</label>
                    <input type="date" name="FechaConsumo" class="form-control"
                           value="{{ old('FechaConsumo') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Fab. Huelva</label>
                    <input type="date" name="FechaFabricacionHuelva" class="form-control"
                           value="{{ old('FechaFabricacionHuelva') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label" title="Hora Estimada Consumo L1">H.E.C L1</label>
                    <input type="time" name="HoraEstimadaConsumoL1" class="form-control"
                           value="{{ old('HoraEstimadaConsumoL1') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label" title="Hora Estimada Consumo L2">H.E.C L2</label>
                    <input type="time" name="HoraEstimadaConsumoL2" class="form-control"
                           value="{{ old('HoraEstimadaConsumoL2') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg"></i> Añadir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Tabla --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover align-middle mb-0" style="font-size:0.85rem; white-space:nowrap;">
            <thead>
                <tr>
                    <th>Nº Cisterna</th>
                    <th>Destino</th>
                    <th>Fecha Consumo</th>
                    <th>Fecha Fab. Huelva</th>
                    <th title="Hora Estimada Consumo Línea 1">H.E.C L1</th>
                    <th title="Hora Estimada Consumo Línea 2">H.E.C L2</th>
                    @if(auth()->user()->isAdmin())
                        <th>Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($filas as $fila)
                    <tr>
                        <td>{{ str_pad($fila['NumeroCisterna'], 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $fila['Destino'] ?: '—' }}</td>
                        <td>{{ $fila['FechaConsumo'] ? \Carbon\Carbon::parse($fila['FechaConsumo'])->format('d/m/Y') : '—' }}</td>
                        <td>{{ $fila['FechaFabricacionHuelva'] ? \Carbon\Carbon::parse($fila['FechaFabricacionHuelva'])->format('d/m/Y') : '—' }}</td>
                        <td>{{ $fila['HoraEstimadaConsumoL1'] ?: '—' }}</td>
                        <td>{{ $fila['HoraEstimadaConsumoL2'] ?: '—' }}</td>
                        @if(auth()->user()->isAdmin())
                            <td>
                                <a href="{{ route('planificacion.edit', $fila['id']) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('planificacion.destroy', $fila['id']) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('¿Eliminar esta fila?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            No hay filas en la planificación.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection