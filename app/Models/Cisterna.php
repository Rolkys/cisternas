<?php

/**
 * DOC: Proyecto Cisternas
 * Archivo personalizado del dominio de negocio.
 * Contiene logica especifica de gestion de cisternas/usuarios/planificacion.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Cisterna: gestion datos cisternas transporte (fillable/casts).
 */
class Cisterna extends Model
{
    protected $primaryKey = 'IdCisterna';

    protected $fillable = [
        'OF',
        'NumeroCisterna',
        'Origen',
        'Destino',
        'Matricula',
        'MatriculaCisterna',
        'Conductor',
        'Telefono',
        'Transporte',
        'FechaFabricacionHuelva',
        'HoraSalida',
        'FechaEntradaMG',
        'HoraLlegadaEstimada',
        'FechaConsumoMG',
        'HoraEstimadaConsumoL1',
        'HoraEstimadaConsumoL2',
        'HoraRealConsumoL1',
        'HoraRealConsumoL2',
        'GlobalGAP',
        'FDA',
        'Observaciones',
        'Incidencias'
    ];

    protected $casts = [
        // Fechas/horas
        'FechaFabricacionHuelva'    => 'datetime',
        'HoraSalida'                => 'datetime',
        'FechaEntradaMG'            => 'datetime',
        'HoraLlegadaEstimada'       => 'datetime',
        'FechaConsumoMG'            => 'datetime',
        'HoraEstimadaConsumoL1'     => 'datetime',
        'HoraEstimadaConsumoL2'     => 'datetime',
        'HoraRealConsumoL1'         => 'datetime',
        'HoraRealConsumoL2'         => 'datetime',

        // Booleanos
        'GlobalGAP'                 => 'boolean',
        'FDA'                       => 'boolean',
    ];

    protected $dateFormat = 'Ymd H:i:s';

    public function freshTimestamp()
    {
        return now()->format('Ymd H:i:s');
    }
}


