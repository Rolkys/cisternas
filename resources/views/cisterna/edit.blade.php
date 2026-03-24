@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        {{ auth()->user()->isOperario() ? '✏️ Editar Consumo' : '✏️ Editar Cisterna' }}
                    </h4>
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

                    @php
                        $user = auth()->user();
                        $isOperario = $user->isOperario();
                        $cisternaId = $cisterna->IdCisterna ?? $cisterna->id;
                    @endphp

                    <form method="POST" action="{{ route('cisterna.update', $cisternaId) }}">
                        @csrf
                        @method('PUT')

                        @if(!$isOperario)
                            {{-- ==================== CAMPOS EDITABLES POR ADMIN, ROOT Y USER ==================== --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="OF" class="form-label">OF *</label>
                                    <input type="number" class="form-control @error('OF') is-invalid @enderror" 
                                           id="OF" name="OF" value="{{ old('OF', $cisterna->OF) }}" required>
                                    @error('OF')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="NumeroCisterna" class="form-label">Número Cisterna *</label>
                                    <input type="number" class="form-control @error('NumeroCisterna') is-invalid @enderror" 
                                           id="NumeroCisterna" name="NumeroCisterna" value="{{ old('NumeroCisterna', $cisterna->NumeroCisterna) }}" required>
                                    @error('NumeroCisterna')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="Conductor" class="form-label">Conductor *</label>
                                    <input type="text" class="form-control @error('Conductor') is-invalid @enderror" 
                                           id="Conductor" name="Conductor" value="{{ old('Conductor', $cisterna->Conductor) }}" required>
                                    @error('Conductor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Matricula" class="form-label">Matrícula</label>
                                    <input type="text" class="form-control @error('Matricula') is-invalid @enderror" 
                                           id="Matricula" name="Matricula" value="{{ old('Matricula', $cisterna->Matricula) }}">
                                    @error('Matricula')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="MatriculaCisterna" class="form-label">Matrícula Cisterna</label>
                                    <input type="text" class="form-control @error('MatriculaCisterna') is-invalid @enderror" 
                                           id="MatriculaCisterna" name="MatriculaCisterna" value="{{ old('MatriculaCisterna', $cisterna->MatriculaCisterna) }}">
                                    @error('MatriculaCisterna')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control @error('Telefono') is-invalid @enderror" 
                                           id="Telefono" name="Telefono" value="{{ old('Telefono', $cisterna->Telefono) }}">
                                    @error('Telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="Origen" class="form-label">Origen</label>
                                    <input type="text" class="form-control @error('Origen') is-invalid @enderror" 
                                           id="Origen" name="Origen" value="{{ old('Origen', $cisterna->Origen) }}">
                                    @error('Origen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Destino" class="form-label">Destino</label>
                                    <input type="text" class="form-control @error('Destino') is-invalid @enderror" 
                                           id="Destino" name="Destino" value="{{ old('Destino', $cisterna->Destino) }}">
                                    @error('Destino')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="Transporte" class="form-label">Transporte</label>
                                    <input type="text" class="form-control @error('Transporte') is-invalid @enderror" 
                                           id="Transporte" name="Transporte" value="{{ old('Transporte', $cisterna->Transporte) }}">
                                    @error('Transporte')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="FechaConsumoMG" class="form-label">Fecha Consumo MG</label>
                                    <input type="date" class="form-control @error('FechaConsumoMG') is-invalid @enderror" 
                                           id="FechaConsumoMG" name="FechaConsumoMG" value="{{ old('FechaConsumoMG', $cisterna->FechaConsumoMG?->format('Y-m-d')) }}">
                                    @error('FechaConsumoMG')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="FechaFabricacionHuelva" class="form-label">Fecha Fabricación Huelva</label>
                                    <input type="date" class="form-control @error('FechaFabricacionHuelva') is-invalid @enderror" 
                                           id="FechaFabricacionHuelva" name="FechaFabricacionHuelva" value="{{ old('FechaFabricacionHuelva', $cisterna->FechaFabricacionHuelva?->format('Y-m-d')) }}">
                                    @error('FechaFabricacionHuelva')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="HoraSalida" class="form-label">Hora Salida</label>
                                    <input type="datetime-local" class="form-control @error('HoraSalida') is-invalid @enderror" 
                                           id="HoraSalida" name="HoraSalida" value="{{ old('HoraSalida', $cisterna->HoraSalida?->format('Y-m-d\TH:i')) }}">
                                    @error('HoraSalida')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="FechaEntradaMG" class="form-label">Fecha Entrada MG</label>
                                    <input type="date" class="form-control @error('FechaEntradaMG') is-invalid @enderror" 
                                           id="FechaEntradaMG" name="FechaEntradaMG" value="{{ old('FechaEntradaMG', $cisterna->FechaEntradaMG?->format('Y-m-d')) }}">
                                    @error('FechaEntradaMG')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="Incidencias" class="form-label">Incidencias</label>
                                    <textarea class="form-control @error('Incidencias') is-invalid @enderror" 
                                              id="Incidencias" name="Incidencias" rows="2">{{ old('Incidencias', $cisterna->Incidencias) }}</textarea>
                                    @error('Incidencias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="GlobalGAP" name="GlobalGAP" value="1" 
                                               {{ old('GlobalGAP', $cisterna->GlobalGAP) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="GlobalGAP">Global GAP</label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="FDA" name="FDA" value="1" 
                                               {{ old('FDA', $cisterna->FDA) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="FDA">FDA</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                        @else
                            {{-- ==================== CAMPOS BLOQUEADOS PARA OPERARIO ==================== --}}
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Como operario, solo puedes editar los campos de consumo y observaciones.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">OF</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->OF }}" disabled>
                                    <small class="text-muted">Campo no editable para operarios</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Número Cisterna</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->NumeroCisterna }}" disabled>
                                    <small class="text-muted">Campo no editable para operarios</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Conductor</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->Conductor }}" disabled>
                                    <small class="text-muted">Campo no editable para operarios</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Matrícula</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->Matricula }}" disabled>
                                    <small class="text-muted">Campo no editable para operarios</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Origen</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->Origen }}" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Destino</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->Destino }}" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha Consumo MG</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->FechaConsumoMG?->format('d/m/Y') ?? '—' }}" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fecha Entrada MG</label>
                                    <input type="text" class="form-control bg-light" value="{{ $cisterna->FechaEntradaMG?->format('d/m/Y') ?? '—' }}" disabled>
                                </div>
                            </div>

                            <hr class="my-4">
                        @endif

                        {{-- ==================== CAMPOS EDITABLES POR TODOS ==================== --}}
                        <h5 class="mb-3">📊 Datos de Consumo</h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="HoraRealConsumoL1" class="form-label">Hora Real Consumo L1</label>
                                <input type="time" class="form-control @error('HoraRealConsumoL1') is-invalid @enderror" 
                                       id="HoraRealConsumoL1" name="HoraRealConsumoL1" 
                                       value="{{ old('HoraRealConsumoL1', $cisterna->HoraRealConsumoL1 ? \Carbon\Carbon::parse($cisterna->HoraRealConsumoL1)->format('H:i') : '') }}">
                                @error('HoraRealConsumoL1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="HoraRealConsumoL2" class="form-label">Hora Real Consumo L2</label>
                                <input type="time" class="form-control @error('HoraRealConsumoL2') is-invalid @enderror" 
                                       id="HoraRealConsumoL2" name="HoraRealConsumoL2" 
                                       value="{{ old('HoraRealConsumoL2', $cisterna->HoraRealConsumoL2 ? \Carbon\Carbon::parse($cisterna->HoraRealConsumoL2)->format('H:i') : '') }}">
                                @error('HoraRealConsumoL2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="Observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control @error('Observaciones') is-invalid @enderror" 
                                          id="Observaciones" name="Observaciones" rows="2">{{ old('Observaciones', $cisterna->Observaciones) }}</textarea>
                                @error('Observaciones')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('cisterna.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> 
                                {{ $isOperario ? 'Actualizar Consumo' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection