@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nueva Cisterna</h4>
    <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('cisterna.store') }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label">OF <span class="text-danger">*</span></label>
                    <input type="number" name="OF" class="form-control"
                        value="{{ old('OF') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Nº Cisterna <span class="text-danger">*</span></label>
                    <input type="number" name="NumeroCisterna" class="form-control"
                        value="{{ old('NumeroCisterna') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Conductor <span class="text-danger">*</span></label>
                    <input type="text" name="Conductor" class="form-control"
                        value="{{ old('Conductor') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Origen</label>
                    <input type="text" name="Origen" class="form-control"
                        value="{{ old('Origen') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Destino</label>
                    <input type="text" name="Destino" class="form-control"
                        value="{{ old('Destino') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Transporte</label>
                    <input type="text" name="Transporte" class="form-control"
                        value="{{ old('Transporte') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Matrícula Camión</label>
                    <input type="text" name="Matricula" class="form-control"
                        value="{{ old('Matricula') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Matrícula Cisterna</label>
                    <input type="text" name="MatriculaCisterna" class="form-control"
                        value="{{ old('MatriculaCisterna') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="Telefono" class="form-control"
                        value="{{ old('Telefono') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Salida</label>
                    <input type="datetime-local" name="HoraSalida" class="form-control"
                        value="{{ old('HoraSalida') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha Entrada MG</label>
                    <input type="datetime-local" name="FechaEntradaMG" class="form-control"
                        value="{{ old('FechaEntradaMG') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Llegada Estimada</label>
                    <input type="datetime-local" name="HoraLlegadaEstimada" class="form-control"
                        value="{{ old('HoraLlegadaEstimada') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha Consumo MG</label>
                    <input type="date" name="FechaConsumoMG" class="form-control"
                        value="{{ old('FechaConsumoMG') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Estimada Consumo L1</label>
                    <input type="datetime-local" name="HoraEstimadaConsumoL1" class="form-control"
                        value="{{ old('HoraEstimadaConsumoL1') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Estimada Consumo L2</label>
                    <input type="datetime-local" name="HoraEstimadaConsumoL2" class="form-control"
                        value="{{ old('HoraEstimadaConsumoL2') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Real Consumo L1</label>
                    <input type="datetime-local" name="HoraRealConsumoL1" class="form-control"
                        value="{{ old('HoraRealConsumoL1') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Hora Real Consumo L2</label>
                    <input type="datetime-local" name="HoraRealConsumoL2" class="form-control"
                        value="{{ old('HoraRealConsumoL2') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">GlobalGAP</label>
                    <select name="GlobalGAP" class="form-select">
                        <option value="">— Sin definir —</option>
                        <option value="1" {{ old('GlobalGAP') == '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ old('GlobalGAP') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">FDA</label>
                    <select name="FDA" class="form-select">
                        <option value="">— Sin definir —</option>
                        <option value="1" {{ old('FDA') == '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ old('FDA') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea name="Observaciones" class="form-control" rows="3">{{ old('Observaciones') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Incidencias</label>
                    <textarea name="Incidencias" class="form-control" rows="3">{{ old('Incidencias') }}</textarea>
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
                <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>

        </form>
    </div>
</div>
@endsection