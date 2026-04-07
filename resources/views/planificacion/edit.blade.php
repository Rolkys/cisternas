{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar fila planificación</h4>
    <a href="{{ route('planificacion.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('planificacion.update', $fila['id']) }}">
            @csrf
            @method('PATCH')

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Nº Cisterna</label>
                    <input type="number" name="NumeroCisterna" class="form-control"
                           value="{{ old('NumeroCisterna', $fila['NumeroCisterna']) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Destino</label>
                    <input type="text" name="Destino" class="form-control"
                           value="{{ old('Destino', $fila['Destino']) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Consumo</label>
                    <input type="date" name="FechaConsumo" class="form-control"
                           value="{{ old('FechaConsumo', $fila['FechaConsumo']) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Fab. Huelva</label>
                    <input type="date" name="FechaFabricacionHuelva" class="form-control"
                           value="{{ old('FechaFabricacionHuelva', $fila['FechaFabricacionHuelva']) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" title="Hora Estimada Consumo L1">H.E.C L1</label>
                    <input type="time" name="HoraEstimadaConsumoL1" class="form-control"
                           value="{{ old('HoraEstimadaConsumoL1', $fila['HoraEstimadaConsumoL1']) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" title="Hora Estimada Consumo L2">H.E.C L2</label>
                    <input type="time" name="HoraEstimadaConsumoL2" class="form-control"
                           value="{{ old('HoraEstimadaConsumoL2', $fila['HoraEstimadaConsumoL2']) }}">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
                <a href="{{ route('planificacion.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
