<?php

/**
 * DOC: Proyecto Cisternas
 * Archivo personalizado del dominio de negocio.
 * Contiene logica especifica de gestion de cisternas/usuarios/planificacion.
 */

namespace App\Services;

use App\Models\Cisterna;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Servicio import cisternas desde Excel (preview + import).
 */
class ExcelImportService
{
    // PREVIEW
    /**
     * Genera una previsualizacion de filas extraidas desde un Excel.
     */
    public function preview(string $filePath, bool $skipExisting = true): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $preview = [];

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $name = trim($worksheet->getTitle());

            try {
                $data = $this->extractFromSheet($worksheet);

                // Ignorar hojas sin datos utiles
                if (empty($data['OF']) && empty($data['NumeroCisterna']) && empty($data['Conductor'])) {
                    continue;
                }

                // Opcionalmente ocultar ya existentes en preview
                if ($skipExisting) {
                    $existe = Cisterna::where('OF', $data['OF'])
                        ->where('NumeroCisterna', $data['NumeroCisterna'])
                        ->exists();

                    if ($existe) {
                        continue;
                    }
                }

                $data['_hoja'] = $name;
                $data['_error'] = null;
                $preview[] = $data;
            } catch (\Throwable $e) {
                $preview[] = [
                    '_hoja' => $name,
                    '_error' => $e->getMessage(),
                ];
            }
        }

        return $preview;
    }

    // IMPORT
    /**
     * Importa filas de un Excel aplicando validaciones de integridad.
     */
    public function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $imported = 0;
        $errors = [];

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $name = $worksheet->getTitle();

            try {
                $data = $this->extractFromSheet($worksheet);

                if (empty($data['OF']) && empty($data['NumeroCisterna']) && empty($data['Conductor'])) {
                    continue;
                }

                $existe = Cisterna::where('OF', $data['OF'])
                    ->where('NumeroCisterna', $data['NumeroCisterna'])
                    ->exists();

                if ($existe) {
                    $errors[] = "Hoja {$name}: Ya existe una cisterna con OF {$data['OF']} y N {$data['NumeroCisterna']} - omitida.";
                    continue;
                }

                Cisterna::create($data);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Hoja {$name}: " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    // EXTRACT
    /**
     * Extrae y transforma los datos de una hoja concreta del Excel.
     */
    private function extractFromSheet(Worksheet $ws): array
    {
        $observaciones = $this->buildObservaciones($ws);
        $fechaConsumo = $this->parseDate($ws->getCell('J16')->getValue());

        return [
            'OF' => (int) $this->cellValue($ws, 'M3'),
            'NumeroCisterna' => (int) $this->cellValue($ws, 'M2'),
            'Conductor' => $this->cellValue($ws, 'H16'),
            'Telefono' => $this->cellValue($ws, 'H17'),
            'Origen' => $this->cellValue($ws, 'M9'),
            'Destino' => $this->cellValue($ws, 'M10'),
            'Matricula' => $this->cellValue($ws, 'M5'),
            'MatriculaCisterna' => $this->cellValue($ws, 'M6'),
            'Transporte' => $this->cellValue($ws, 'M7'),
            'FechaFabricacionHuelva' => $this->parseDate($ws->getCell('M1')->getValue()),
            'HoraSalida' => $this->parseDateTime(
                $ws->getCell('D16')->getValue(),
                $ws->getCell('D17')->getValue()
            ),
            'FechaEntradaMG' => $fechaConsumo,
            'HoraLlegadaEstimada' => $this->parseDateTime(
                $ws->getCell('J16')->getValue(),
                $ws->getCell('J17')->getValue()
            ),
            'FechaConsumoMG' => $fechaConsumo,
            'HoraEstimadaConsumoL1' => null,
            'HoraEstimadaConsumoL2' => null,
            'HoraRealConsumoL1' => null,
            'HoraRealConsumoL2' => null,
            'GlobalGAP' => false,
            'FDA' => false,
            'Observaciones' => $observaciones ?: null,
            'Incidencias' => null,
        ];
    }

    // HELPER: valor de celda
    /**
     * Obtiene el valor limpio de una celda del Excel.
     */
    private function cellValue(Worksheet $ws, string $coord): string
    {
        $raw = $ws->getCell($coord)->getCalculatedValue();
        return trim((string) ($raw ?? ''));
    }

    // HELPER: observaciones
    /**
     * Construye el texto de observaciones a partir de varias celdas.
     */
    private function buildObservaciones(Worksheet $ws): ?string
    {
        $partes = [];

        $concepto = $this->cellValue($ws, 'C14');
        $brix = $this->cellValue($ws, 'H14');
        $kilos = $this->cellValue($ws, 'L14');
        $precintos = $this->cellValue($ws, 'D15');
        $tara = $this->cellValue($ws, 'J15');

        if ($concepto !== '') $partes[] = 'Concepto: ' . $concepto;
        if ($brix !== '') $partes[] = 'BRIX: ' . $brix;
        if ($kilos !== '') $partes[] = 'Kilos: ' . $kilos;
        if ($precintos !== '') $partes[] = 'Precintos: ' . $precintos;
        if ($tara !== '') $partes[] = 'Tara: ' . $tara;

        return empty($partes) ? null : implode(' | ', $partes);
    }

    // HELPERS: fechas
    /**
     * Convierte un valor de celda en fecha compatible con la base de datos.
     */
    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            $dt = Date::excelToDateTimeObject((float) $value);
            return $dt->format('Ymd H:i:s');
        }

        if ($value instanceof \DateTime) {
            return $value->format('Ymd H:i:s');
        }

        try {
            return (new \DateTime((string) $value))->format('Ymd H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Combina fecha y hora de celdas Excel en un datetime normalizado.
     */
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

        return $date->format('Ymd H:i:s');
    }
}

