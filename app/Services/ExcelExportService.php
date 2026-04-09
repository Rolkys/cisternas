<?php

/**
 * DOC: Proyecto Cisternas
 * Archivo personalizado del dominio de negocio.
 * Contiene logica especifica de gestion de cisternas/usuarios/planificacion.
 */

namespace App\Services;

use App\Models\Cisterna;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Servicio export cisternas a Excel con colores/estilos.
 */
class ExcelExportService
{

    /**
     * Exporta los datos filtrados a un archivo Excel.
     */
    public function export($cisternas)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cisternas');


        // ======== CABECERA ========
        $headers = [
            'A' => 'OF',                    //A
            'B' => 'Nº Ciseterna',          //B
            'C' => 'Confuctor',             //C
            'D' => 'Teléfono',              //D
            'E' => 'Origen',                //E
            'F' => 'Destino',               //F
            'G' => 'Matrícula Camión',      //G
            'H' => 'Matrícula Cisterna',    //H
            'I' => 'Transporte',            //I
            'J' => 'Hora Salida',           //J
            'K' => 'Fecha Entrada MG',      //K
            'L' => 'Fecha Consumno MG',     //L
            'M' => 'H. Est. Consumo L1',    //M
            'N' => 'H. Est. Consumo L2',    //N
            'O' => 'H. Real Consunmo L1',   //O
            'P' => 'H. Real Consunmo L2',   //P
            'Q' => 'GlobalGAP',             //Q
            'R' => 'FDA',                   //R
            'S' => 'Observaciones',         //S
            'T' => 'Incidencias',           //T
        ];

        foreach($headers as $col => $title){
            $sheet->setCellValue($col . '1', $title);
        }

        // Estilo cabecera - fondo oscuro texto blanco
        $headerStyle = $sheet->getStyle('A1:T1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF');
        $headerStyle->getFill()->setFillType(Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FF0F2130');
        $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ======== DATOS ========
        $row = 2;
        $hoy = now()->startOfDay();

        foreach($cisternas as $cisterna){
            $sheet->setCellValue('A' . $row, $cisterna->OF);
            $sheet->setCellValue('B' . $row, str_pad($cisterna->NumeroCisterna, 4, '0', STR_PAD_LEFT));
            $sheet->setCellValue('C' . $row, $cisterna->Conductor);
            $sheet->setCellValue('D' . $row, $cisterna->Telefono);
            $sheet->setCellValue('E' . $row, $cisterna->Origen);
            $sheet->setCellValue('F' . $row, $cisterna->Destino);
            $sheet->setCellValue('G' . $row, $cisterna->Matricula);
            $sheet->setCellValue('H' . $row, $cisterna->MatriculaCisterna);
            $sheet->setCellValue('I' . $row, $cisterna->Transporte);
            $sheet->setCellValue('J' . $row, $cisterna->HoraSalida ? $cisterna->HoraSalida->format('d/m/Y H:i') : null);
            $sheet->setCellValue('K' . $row, $cisterna->FechaEntradaMG ? $cisterna->FechaEntradaMG->format('d/m/Y H:i') : null);
            $sheet->setCellValue('L' . $row, $cisterna->FechaConsumoMG ? $cisterna->FechaConsumoMG->format('d/m/Y') : null);
            $sheet->setCellValue('M' . $row, $cisterna->HoraEstimadaConsumoL1 ? $cisterna->HoraEstimadaConsumoL1->format('H:i') : null);
            $sheet->setCellValue('N' . $row, $cisterna->HoraEstimadaConsumoL2 ? $cisterna->HoraEstimadaConsumoL2->format('H:i') : null);
            $sheet->setCellValue('O' . $row, $cisterna->HoraRealConsumoL1 ? $cisterna->HoraRealConsumoL1->format('H:i') : null);
            $sheet->setCellValue('P' . $row, $cisterna->HoraRealConsumoL2 ? $cisterna->HoraRealConsumoL2->format('H:i') : null);
            $sheet->setCellValue('Q' . $row, $cisterna->GlobalGAP === null ? '—' : ($cisterna->GlobalGAP ? 'Sí' : 'No'));
            $sheet->setCellValue('R' . $row, $cisterna->FDA === null ? '—' : ($cisterna->FDA ? 'Sí' : 'No'));
            $sheet->setCellValue('S' . $row, $cisterna->Observaciones);
            $sheet->setCellValue('T' . $row, $cisterna->Incidencias);

            // Color de fila según stado - igual que en la vista
            $color = null;
            if ($cisterna->Incidencias) {
                $color = 'FFFF746C'; // rojo
            } elseif ($cisterna->HoraRealConsumoL1 || $cisterna->HoraRealConsumoL2) {
                $color = 'FFadebb3'; // verde
            } elseif ($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isSameDay($hoy)) {
                $color = 'FF90D5FF'; // azul
            } elseif ($cisterna->FechaConsumoMG && $cisterna->FechaConsumoMG->isAfter($hoy)) {
                $color = 'FFFFEE8C'; // amarillo
            }
            
            if ($color) {
                $sheet->getStyle("A{$row}:T{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => $color],
                    ],
                ]);
            }
            $row++;
        }

        // ======== ANCHO DE COLUMNAS ========
        foreach(range('A', 'T') as $col){
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ======== GENERAR ARCHIVO ========
        $write = new Xlsx($spreadsheet);
        $filename = 'cisternas_' . now()->format('Y-m-d_H-i') . '.xlsx';
        $tempPath = storage_path('app/private/temp/' . $filename);
        
        if(!file_exists(dirname($tempPath))){
            mkdir(dirname($tempPath), 0755, true);
        }

        $write->save($tempPath);

        return $tempPath;
        
    }

}
