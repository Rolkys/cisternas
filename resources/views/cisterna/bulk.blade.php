{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicacion. --}}
@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-file-earmark-excel"></i> Importación masiva de Excel</h4>
    <a href="{{ route('cisterna.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <p class="text-muted">
            Sube el archivo <strong>.xlsx</strong> con las cisternas.
            Se procesarán automáticamente todas las hojas <strong>C1, C2, C3...</strong>
            Las hojas duplicadas (mismo OF + Nº Cisterna) se omitirán.
        </p>

        <form method="POST" action="{{ route('cisterna.bulk.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Archivo Excel (.xlsx)</label>
                <input type="file" name="excel" class="form-control" accept=".xlsx,.xls" required>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-upload"></i> Importar
            </button>
        </form>

        <hr>
        <h6 class="mt-3">Campos que se importan automáticamente:</h6>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>M3</strong> → OF</li>
                    <li class="list-group-item"><strong>M2</strong> → Nº Cisterna</li>
                    <li class="list-group-item"><strong>H16</strong> → Conductor</li>
                    <li class="list-group-item"><strong>H17</strong> → Teléfono</li>
                    <li class="list-group-item"><strong>M9</strong> → Origen</li>
                    <li class="list-group-item"><strong>M10</strong> → Destino</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>M5</strong> → Matrícula camión</li>
                    <li class="list-group-item"><strong>M6</strong> → Matrícula cisterna</li>
                    <li class="list-group-item"><strong>M7</strong> → Transporte</li>
                    <li class="list-group-item"><strong>M1</strong> → Fecha fabricación Huelva</li>
                    <li class="list-group-item"><strong>D16</strong> → Fecha salida</li>
                    <li class="list-group-item"><strong>D17</strong> → Hora salida</li>
                    <li class="list-group-item"><strong>J16</strong> → Fecha entrada MG</li>
                    <li class="list-group-item"><strong>J17</strong> → Hora entrada MG</li>
                    <li class="list-group-item"><strong>C14, H14, L14, D15, J15</strong> → Observaciones</li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection
