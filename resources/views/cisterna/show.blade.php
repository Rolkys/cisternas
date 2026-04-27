
{{-- DOC: Proyecto Cisternas | Vista personalizada de la aplicación. --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e10734 !important; color: #ffffff !important; border-color: #e10734 !important;">
                    <h4 class="mb-0">🔍 Detalles de Cisterna #{{ $cisterna->NumeroCisterna }}</h4>
                    <div>
                        @if(!auth()->user()->isOperario())
                            <a href="{{ route('cisterna.edit', $cisterna->IdCisterna ?? $cisterna->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        @endif
                        <a href="{{ route('cisterna.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID</th>
                                    <td>{{ $cisterna->IdCisterna ?? $cisterna->id }}</td>
                                </tr>
                                <tr>
                                    <th>OF</th>
                                    <td>{{ $cisterna->OF }}</td>
                                </tr>
                                <tr>
                                    <th>Número Cisterna</th>
                                    <td>{{ $cisterna->NumeroCisterna }}</td>
                                </tr>
                                <tr>
                                    <th>Conductor</th>
                                    <td>{{ $cisterna->Conductor }}</td>
                                </tr>
                                <tr>
                                    <th>Matrícula</th>
                                    <td>{{ $cisterna->Matricula ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Matrícula Cisterna</th>
                                    <td>{{ $cisterna->MatriculaCisterna ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono</th>
                                    <td>{{ $cisterna->Telefono ?: '—' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Origen</th>
                                    <td>{{ $cisterna->Origen ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Destino</th>
                                    <td>{{ $cisterna->Destino ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Transporte</th>
                                    <td>{{ $cisterna->Transporte ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Consumo MG</th>
                                    <td>{{ optional($cisterna->FechaConsumoMG)->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Fabricación Huelva</th>
                                    <td>{{ optional($cisterna->FechaFabricacionHuelva)->format('d/m/Y') ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Hora Salida</th>
                                    <td>{{ optional($cisterna->HoraSalida)->format('d/m/Y H:i') ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Entrada MG</th>
                                    <td>{{ optional($cisterna->FechaEntradaMG)->format('d/m/Y') ?? '—' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>📊 Datos de Consumo</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Hora Real Consumo L1</th>
$cisterna->HoraRealConsumoL1->format('H:i')
                                </tr>
                                <tr>
                                    <th>Hora Real Consumo L2</th>
$cisterna->HoraRealConsumoL2->format('H:i')
                                </tr>
                                <tr>
                                    <th>Observaciones</th>
                                    <td>{{ $cisterna->Observaciones ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Incidencias</th>
                                    <td>
                                        @if($cisterna->Incidencias)
                                            <span class="badge bg-danger">{{ $cisterna->Incidencias }}</span>
                                        @else
                                            <span class="badge bg-success">Sin incidencias</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Global GAP</th>
                                    <td>{{ $cisterna->GlobalGAP ? '✅ Sí' : '❌ No' }}</td>
                                </tr>
                                <tr>
                                    <th>FDA</th>
                                    <td>{{ $cisterna->FDA ? '✅ Sí' : '❌ No' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

