{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header" style="background-color: #e10734 !important; color: #ffffff !important; border-color: #e10734 !important;">
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
                            {{-- Si NO operario: muestra todos campos editables (admin/root/user) --}}
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
                                           id="FechaConsumoMG" name="FechaConsumoMG"
                                           value="{{ old('FechaConsumoMG', optional($cisterna->FechaConsumoMG)->format('Y-m-d')) }}">
                                    @error('FechaConsumoMG')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
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
                            {{-- Si operario: campos bloqueados, solo consumo editable --}}
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
                                    <input type="text" class="form-control bg-light"
                                           value="{{ optional($cisterna->FechaConsumoMG)->format('d/m/Y') ?? '—' }}" disabled>
                                </div>
                            </div>

                            <hr class="my-4">
                        @endif

                        {{-- ==================== CAMPOS EDITABLES POR TODOS ==================== --}}
                        <h5 class="mb-3">📊 Datos de Consumo</h5>

                        @if(auth()->user()->isAdmin())
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="HoraEstimadaConsumoL1" class="form-label">H. Estimada Consumo L1</label>
                                <input type="time" class="form-control @error('HoraEstimadaConsumoL1') is-invalid @enderror"
                                       id="HoraEstimadaConsumoL1" name="HoraEstimadaConsumoL1"
                                       value="{{ old('HoraEstimadaConsumoL1', optional($cisterna->HoraEstimadaConsumoL1)->format('H:i')) }}">
                                @error('HoraEstimadaConsumoL1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="HoraEstimadaConsumoL2" class="form-label">H. Estimada Consumo L2</label>
                                <input type="time" class="form-control @error('HoraEstimadaConsumoL2') is-invalid @enderror"
                                       id="HoraEstimadaConsumoL2" name="HoraEstimadaConsumoL2"
                                       value="{{ old('HoraEstimadaConsumoL2', optional($cisterna->HoraEstimadaConsumoL2)->format('H:i')) }}">
                                @error('HoraEstimadaConsumoL2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="HoraRealConsumoL1" class="form-label">H. Real Consumo L1</label>
                                <input type="time" class="form-control @error('HoraRealConsumoL1') is-invalid @enderror"
                                       id="HoraRealConsumoL1" name="HoraRealConsumoL1"
                                       value="{{ old('HoraRealConsumoL1', optional($cisterna->HoraRealConsumoL1)->format('H:i')) }}">
                                @error('HoraRealConsumoL1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="HoraRealConsumoL2" class="form-label">H. Real Consumo L2</label>
                                <input type="time" class="form-control @error('HoraRealConsumoL2') is-invalid @enderror"
                                       id="HoraRealConsumoL2" name="HoraRealConsumoL2"
                                       value="{{ old('HoraRealConsumoL2', optional($cisterna->HoraRealConsumoL2)->format('H:i')) }}">
                                @error('HoraRealConsumoL2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    // HEC mutex: L1 <-> L2
    const hecL1 = document.getElementById('HoraEstimadaConsumoL1');
    const hecL2 = document.getElementById('HoraEstimadaConsumoL2');

    if (hecL1 && hecL2) {
        if (hecL1.value) { hecL2.disabled = true; }
        else if (hecL2.value) { hecL1.disabled = true; }

        hecL1.addEventListener('input', function () {
            if (this.value) { hecL2.value = ''; hecL2.disabled = true; }
            else { hecL2.disabled = false; }
        });
        hecL2.addEventListener('input', function () {
            if (this.value) { hecL1.value = ''; hecL1.disabled = true; }
            else { hecL1.disabled = false; }
        });
    }

    // HRC mutex: L1 <-> L2
    const hrcL1 = document.getElementById('HoraRealConsumoL1');
    const hrcL2 = document.getElementById('HoraRealConsumoL2');

    if (hrcL1 && hrcL2) {
        if (hrcL1.value) { hrcL2.disabled = true; }
        else if (hrcL2.value) { hrcL1.disabled = true; }

        hrcL1.addEventListener('input', function () {
            if (this.value) { hrcL2.value = ''; hrcL2.disabled = true; }
            else { hrcL2.disabled = false; }
        });
        hrcL2.addEventListener('input', function () {
            if (this.value) { hrcL1.value = ''; hrcL1.disabled = true; }
            else { hrcL1.disabled = false; }
        });
    }
});
</script>