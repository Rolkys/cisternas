<?php

namespace App\Services;

use App\Models\Cisterna;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelImportService
{
    // ==================== PREVIEW ====================
    public function preview(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $preview     = [];

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $name = trim($worksheet->getTitle());
            if (!preg_match('/^C\d+$/', $name)) continue;

            try {
                $data = $this->extractFromSheet($worksheet);

                // Si ya existe en BD la saltamos directamente sin añadirla al preview
                $existe = Cisterna::where('OF', $data['OF'])
                                  ->where('NumeroCisterna', $data['NumeroCisterna'])
                                  ->exists();

                if ($existe) continue;

                $data['_hoja']  = $name;
                $data['_error'] = null;
                $preview[]      = $data;

            } catch (\Throwable $e) {
                $preview[] = [
                    '_hoja'  => $name,
                    '_error' => $e->getMessage(),
                ];
            }
        }

        return $preview;
    }

    // ==================== IMPORT ====================
    public function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $imported    = 0;
        $errors      = [];

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $name = $worksheet->getTitle();
            if (!preg_match('/^C\d+$/', $name)) continue;

            try {
                $data = $this->extractFromSheet($worksheet);

                if (empty($data['Conductor']) && empty($data['OF'])) continue;

                $existe = Cisterna::where('OF', $data['OF'])
                                  ->where('NumeroCisterna', $data['NumeroCisterna'])
                                  ->exists();

                if ($existe) {
                    $errors[] = "Hoja $name: Ya existe una cisterna con OF {$data['OF']} y Nº {$data['NumeroCisterna']} — omitida.";
                    continue;
                }

                Cisterna::create($data);
                $imported++;

            } catch (\Throwable $e) {
                $errors[] = "Hoja $name: " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors'   => $errors,
        ];
    }

    // ==================== EXTRACT ====================
    private function extractFromSheet(Worksheet $ws): array
    {
        // Construir observaciones de forma segura
        $observaciones = $this->buildObservaciones($ws);

        return [
            'OF'                     => (int) $this->cellValue($ws, 'M3'),
            'NumeroCisterna'         => (int) $this->cellValue($ws, 'M2'),
            'Conductor'              => $this->cellValue($ws, 'H16'),
            'Telefono'               => $this->cellValue($ws, 'H17'),
            'Origen'                 => $this->cellValue($ws, 'M9'),
            'Destino'                => $this->cellValue($ws, 'M10'),
            'Matricula'              => $this->cellValue($ws, 'M5'),
            'MatriculaCisterna'      => $this->cellValue($ws, 'M6'),
            'Transporte'             => $this->cellValue($ws, 'M7'),
            'FechaFabricacionHuelva' => $this->parseDate($ws->getCell('M1')->getValue()),
            'HoraSalida'             => $this->parseDateTime(
                                            $ws->getCell('D16')->getValue(),
                                            $ws->getCell('D17')->getValue()
                                        ),
            'FechaEntradaMG'         => $this->parseDateTime(
                                            $ws->getCell('J16')->getValue(),
                                            $ws->getCell('J17')->getValue()
                                        ),
            'HoraLlegadaEstimada'    => $this->parseDateTime(
                                            $ws->getCell('J16')->getValue(),
                                            $ws->getCell('J17')->getValue()
                                        ),
            'FechaConsumoMG'         => null,
            'HoraEstimadaConsumoL1'  => null,
            'HoraEstimadaConsumoL2'  => null,
            'HoraRealConsumoL1'      => null,
            'HoraRealConsumoL2'      => null,
            'GlobalGAP'              => false,
            'FDA'                    => false,
            'Observaciones'          => $observaciones ?: null,
            'Incidencias'            => null,
        ];
    }

    // ==================== HELPER: leer celda de forma segura ====================
    /**
     * Lee el valor calculado de una celda y lo devuelve siempre como string limpio.
     *
     * Usa getCalculatedValue() en lugar de getValue() para que:
     *  - Las fórmulas devuelvan su resultado, no la expresión "=SUM(...)"
     *  - Los objetos RichText se conviertan a texto plano automáticamente
     *    (PhpSpreadsheet resuelve RichText a string en getCalculatedValue)
     *  - No haya TypeError de trim() con objetos (bug en PHP 8)
     */
    private function cellValue(Worksheet $ws, string $coord): string
    {
        $raw = $ws->getCell($coord)->getCalculatedValue();

        // getCalculatedValue puede devolver null, int, float, bool o string
        // Castear siempre a string antes de trim para evitar TypeError en PHP 8
        return trim((string) ($raw ?? ''));
    }

    // ==================== HELPER: construir observaciones ====================
    /**
     * Construye el string de observaciones de forma segura.
     * Cada pieza se lee con cellValue() que siempre devuelve string.
     * Si el resultado final está vacío devuelve null.
     */
    private function buildObservaciones(Worksheet $ws): ?string
    {
        $partes = [];

        $concepto  = $this->cellValue($ws, 'C14');
        $brix      = $this->cellValue($ws, 'H14');
        $kilos     = $this->cellValue($ws, 'L14');   // usamos cellValue (getCalculatedValue)
        $precintos = $this->cellValue($ws, 'D15');
        $tara      = $this->cellValue($ws, 'J15');

        // Solo añadir la pieza si tiene contenido real (no vacío tras trim)
        if ($concepto  !== '') $partes[] = 'Concepto: '  . $concepto;
        if ($brix      !== '') $partes[] = 'BRIX: '      . $brix;
        if ($kilos     !== '') $partes[] = 'Kilos: '     . $kilos;
        if ($precintos !== '') $partes[] = 'Precintos: ' . $precintos;
        if ($tara      !== '') $partes[] = 'Tara: '      . $tara;

        return empty($partes) ? null : implode(' | ', $partes);
    }

    // ==================== HELPERS: fechas ====================
    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            $dt = Date::excelToDateTimeObject((float) $value);
            return $dt->format('Y-m-d H:i:s');
        }

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        try {
            return (new \DateTime((string) $value))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime($dateValue, $timeValue): ?string
    {
        if ($dateValue === null || $dateValue === '') return null;

        if (is_numeric($dateValue)) {
            $date = Date::excelToDateTimeObject((float) $dateValue);
        } elseif ($dateValue instanceof \DateTime) {
            $date = $dateValue;
        } else {
            try {
                $date = new \DateTime((string) $dateValue);
            } catch (\Exception $e) {
                return null;
            }
        }

        if ($timeValue !== null && $timeValue !== '') {
            if (is_numeric($timeValue)) {
                $time = Date::excelToDateTimeObject((float) $timeValue);
                $date->setTime((int) $time->format('H'), (int) $time->format('i'));
            } elseif ($timeValue instanceof \DateTime) {
                $date->setTime((int) $timeValue->format('H'), (int) $timeValue->format('i'));
            }
        }

        return $date->format('Y-m-d H:i:s');
    }
}