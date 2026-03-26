@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-file-earmark-check"></i> Confirmar importación</h4>
    <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if(empty($preview))
    <div class="alert alert-warning">No se encontraron hojas válidas en el archivo Excel.</div>
@else
<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted mb-3">
            Revisa y edita los datos antes de importar. Marca con el checkbox las que quieres incluir.
            Puedes rellenar las horas estimadas de consumo (H.E.C) en esta pantalla.
        </p>

        <form method="POST" action="{{ route('cisterna.bulk.confirm.store') }}">
            @csrf

            <div class="mb-3 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="toggleTodos(true)">✅ Seleccionar todos</button>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="toggleTodos(false)">❌ Deseleccionar todos</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle"
                        style="font-size:0.82rem; white-space:nowrap;">
                    <thead style="background:#0f2130; color:#fff;">
                        <tr>
                            <th>Incluir</th>
                            <th>Hoja</th>
                            <th>OF</th>
                            <th>Nº Cisterna</th>
                            <th>Conductor</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Matrícula</th>
                            <th>Matrícula Cisterna</th>
                            <th>Transporte</th>
                            <th>Teléfono</th>
                            <th>Fecha Salida</th>
                            <th>Fecha Entrada MG</th>
                            {{-- PROBLEMA 5: Campos H.E.C --}}
                            <th title="Hora Estimada Consumo Línea 1">H.E.C L1</th>
                            <th title="Hora Estimada Consumo Línea 2">H.E.C L2</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview as $i => $fila)
                            @php $error = $fila['_error'] ?? null; @endphp
                            <tr class="{{ $error ? 'table-danger' : '' }}">

                                {{-- Checkbox --}}
                                <td class="text-center">
                                    <input type="checkbox"
                                            name="filas[{{ $i }}][_incluir]"
                                            value="1"
                                            class="form-check-input check-fila"
                                            {{ $error ? '' : 'checked' }}>
                                </td>

                                {{-- Hoja (no editable, solo informativa) --}}
                                <td>
                                    <strong>{{ $fila['_hoja'] }}</strong>
                                    <input type="hidden" name="filas[{{ $i }}][_hoja]"
                                            value="{{ $fila['_hoja'] }}">
                                </td>

                                {{-- Campos editables --}}
                                <td>
                                    <input type="number" name="filas[{{ $i }}][OF]"
                                            value="{{ $fila['OF'] ?? '' }}"
                                            class="form-control form-control-sm" style="width:80px">
                                </td>
                                <td>
                                    <input type="number" name="filas[{{ $i }}][NumeroCisterna]"
                                            value="{{ $fila['NumeroCisterna'] ?? '' }}"
                                            class="form-control form-control-sm" style="width:80px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][Conductor]"
                                            value="{{ $fila['Conductor'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:140px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][Origen]"
                                            value="{{ $fila['Origen'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:100px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][Destino]"
                                            value="{{ $fila['Destino'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:100px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][Matricula]"
                                            value="{{ $fila['Matricula'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:100px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][MatriculaCisterna]"
                                            value="{{ $fila['MatriculaCisterna'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:100px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][Transporte]"
                                            value="{{ $fila['Transporte'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:110px">
                                </td>
                                <td>
                                    <input type="text" name="filas[{{ $i }}][Telefono]"
                                            value="{{ $fila['Telefono'] ?? '' }}"
                                            class="form-control form-control-sm" style="min-width:110px">
                                </td>
                                <td>
                                    <input type="datetime-local" name="filas[{{ $i }}][HoraSalida]"
                                            value="{{ isset($fila['HoraSalida']) && $fila['HoraSalida'] ? \Carbon\Carbon::parse($fila['HoraSalida'])->format('Y-m-d\TH:i') : '' }}"
                                            class="form-control form-control-sm" style="min-width:160px">
                                </td>
                                <td>
                                    <input type="datetime-local" name="filas[{{ $i }}][FechaEntradaMG]"
                                            value="{{ isset($fila['FechaEntradaMG']) && $fila['FechaEntradaMG'] ? \Carbon\Carbon::parse($fila['FechaEntradaMG'])->format('Y-m-d\TH:i') : '' }}"
                                            class="form-control form-control-sm" style="min-width:160px">
                                </td>

                            
                                <td>
                                    <input type="time"
                                            name="filas[{{ $i }}][HoraEstimadaConsumoL1]"
                                            value="{{ isset($fila['HoraEstimadaConsumoL1']) && $fila['HoraEstimadaConsumoL1'] ? \Carbon\Carbon::parse($fila['HoraEstimadaConsumoL1'])->format('H:i') : '' }}"
                                            class="form-control form-control-sm hec-l1"
                                            data-index="{{ $i }}"
                                            style="min-width:100px">
                                </td>
                                <td>
                                    <input type="time"
                                            name="filas[{{ $i }}][HoraEstimadaConsumoL2]"
                                            value="{{ isset($fila['HoraEstimadaConsumoL2']) && $fila['HoraEstimadaConsumoL2'] ? \Carbon\Carbon::parse($fila['HoraEstimadaConsumoL2'])->format('H:i') : '' }}"
                                            class="form-control form-control-sm hec-l2"
                                            data-index="{{ $i }}"
                                            style="min-width:100px">
                                </td>

                                {{-- Estado --}}
                                <td>
                                    @if($error)
                                        <span class="badge bg-danger">{{ $error }}</span>
                                    @else
                                        <span class="badge bg-success">Nueva</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Importar seleccionadas
                </button>
                <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function toggleTodos(estado) {
    document.querySelectorAll('.check-fila').forEach(cb => cb.checked = estado);
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.hec-l1').forEach(function (l1) {
        const idx = l1.dataset.index;
        const l2 = document.querySelector('.hec-l2[data-index="' + idx + '"]');
        if (!l2) return;

        l1.addEventListener('input', function () {
            if (this.value) { l2.value = ''; l2.disabled = true; }
            else { l2.disabled = false; }
        });
        l2.addEventListener('input', function () {
            if (this.value) { l1.value = ''; l1.disabled = true; }
            else { l1.disabled = false; }
        });

        // Estado inicial
        if (l1.value) { l2.disabled = true; }
        else if (l2.value) { l1.disabled = true; }
    });
});
</script>
@endsection
