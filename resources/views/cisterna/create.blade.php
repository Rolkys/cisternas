{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header" style="background-color: #e10734 !important; color: #ffffff !important; border-color: #e10734 !important;">
                    <h4 class="mb-0">Nueva Cisterna</h4>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cisterna.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="numeroCamion" class="form-label">Numero Camion * (ej: 12345)</label>
                                <input type="number" class="form-control @error('numeroCamion') is-invalid @enderror"
                                        id="numeroCamion" name="numeroCamion" value="{{ old('numeroCamion') }}" required>
                                @error('numeroCamion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Conductor" class="form-label">Conductor *</label>
                                <input type="text" class="form-control @error('Conductor') is-invalid @enderror"
                                       id="Conductor" name="Conductor" value="{{ old('Conductor') }}" required>
                                @error('Conductor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="Telefono" class="form-label">Telefono</label>
                                <input type="text" class="form-control @error('Telefono') is-invalid @enderror"
                                       id="Telefono" name="Telefono" value="{{ old('Telefono') }}">
                                @error('Telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Matricula" class="form-label">Matricula</label>
                                <input type="text" class="form-control @error('Matricula') is-invalid @enderror"
                                       id="Matricula" name="Matricula" value="{{ old('Matricula') }}">
                                @error('Matricula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="MatriculaCisterna" class="form-label">Matricula Cisterna</label>
                                <input type="text" class="form-control @error('MatriculaCisterna') is-invalid @enderror"
                                       id="MatriculaCisterna" name="MatriculaCisterna" value="{{ old('MatriculaCisterna') }}">
                                @error('MatriculaCisterna')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Origen" class="form-label">Origen</label>
                                <input type="text" class="form-control @error('Origen') is-invalid @enderror"
                                       id="Origen" name="Origen" value="{{ old('Origen') }}">
                                @error('Origen')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="Destino" class="form-label">Destino</label>
                                <input type="text" class="form-control @error('Destino') is-invalid @enderror"
                                       id="Destino" name="Destino" value="{{ old('Destino') }}">
                                @error('Destino')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Transporte" class="form-label">Transporte</label>
                                <input type="text" class="form-control @error('Transporte') is-invalid @enderror"
                                       id="Transporte" name="Transporte" value="{{ old('Transporte') }}">
                                @error('Transporte')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="FechaConsumoMG" class="form-label">Fecha Consumo MG</label>
                                <input type="date" class="form-control @error('FechaConsumoMG') is-invalid @enderror"
                                       id="FechaConsumoMG" name="FechaConsumoMG" value="{{ old('FechaConsumoMG') }}">
                                @error('FechaConsumoMG')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="Observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('Observaciones') is-invalid @enderror"
                                          id="Observaciones" name="Observaciones" rows="3">{{ old('Observaciones') }}</textarea>
                                @error('Observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Incidencias" class="form-label">Incidencias</label>
                                <textarea class="form-control @error('Incidencias') is-invalid @enderror"
                                          id="Incidencias" name="Incidencias" rows="2">{{ old('Incidencias') }}</textarea>
                                @error('Incidencias')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="row mt-5">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="GlobalGAP" name="GlobalGAP" value="1"
                                                   {{ old('GlobalGAP') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="GlobalGAP">
                                                Global GAP
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="FDA" name="FDA" value="1"
                                                   {{ old('FDA') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="FDA">
                                                FDA
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('cisterna.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cisterna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
