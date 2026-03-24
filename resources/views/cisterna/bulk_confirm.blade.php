@extends('layouts.app')

@section('content')

<div class="d-flex justify-contenxt-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-file-earmark-chechk"></i> Confirmar importación</h4>
    <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

@if(empty($preview))
    <div class="alert alert-warning" No se encontraron hojas válidas en el archivo Excel.></div>
@else
<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted mb-3">
            Revisa las cisternas destacadas. Usa los <Strong>Checkboxes</Strong> para elegir
            cuáles importar. Las marcadas en rojo ya existen en la base de datos.
        </p>

        <form method="POST" action="{{route('cisterna.bulk.confirm.store')}}">
            @csrf

            <div class="mb-3 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="toggleTodos(true)">✅ Seleccionar todos</button>
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="toggleTodos(false)">❌ Deseleccionar todos</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle"
                        style="font-size:0.83rem; white-space:nowrap;">
                    <thead style="background:#0f2130; color#fff;">
                        <tr>
                            <th>Incluir</th>
                            <th>Hojas</th>
                            <th>OF</th>
                            <th>Nº Cisterna</th>
                            <th>Conductor</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Matrícula</th>
                            <th>Transporte</th>
                            <th>Fecha Salida</th>
                            <th>Fecha Entrada MG</th>
                            <th>Estado</th>
                        </tr>
                        <tbody>
                            @foreach($preview as $fila)
                                @php $error = $fila['_error'] ?? null; @endphp
                                <tr class="{{ $error ? 'tabledanger' : ''}}">
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="seleccionados[]"
                                            value="{{ $fila['_hoja'] }}"
                                            class="form-check-input check-fila"
                                            {{ $error ? '' : 'checked' }}>
                                    </td>
                                    <td><strong>{{ $fila['_hoja']}}</strong></td>
                                    <td>{{ isset($fila['NumeroCisterna'])
                                            ? str_pad($fila['NumeroCisterna'], 4, '0', STR_PAD_LEFT)
                                            : '—' }}</td>
                                    <td>{{ $fila['Conductor'] ?? '-' }}</td>
                                    <td>{{ $fila['Origen'] ?? '-' }}</td>
                                    <td>{{ $fila['Destino'] ?? '-' }}</td>
                                    <td>{{ $fila['Matricula'] ?? '-' }}</td>
                                    <td>{{ $fila['Transporte'] ?? '-' }}</td>
                                    <td>{{ isset($fila['HoraSalida']) && $fila['HoraSalida']
                                        ? \Carbon\Carbon::parse($fila['HoraSalida'])->format('d/m/Y H:i')
                                        : '—' }}</td>
                                    <td>{{ isset($fila['FechaEntradaMG']) && $fila['FechaEntradaMG']
                                            ? \Carbon\Carbon::parse($fila['FechaEntradaMG'])->format('d/m/Y H:i')
                                            : '—' }}</td>
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
                    </thead>
                </table>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Importar seleccionadas
                    </button>
                    <a href="{{ route('cisterna.bulk') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function toggleTodos(estado) {
    document.querySelectorAll('.check-fila').forEach(cb => cb.checked = estado);
}
</script>
@endsection