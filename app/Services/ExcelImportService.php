<?php

namespace App\Services;

use App\Models\Cisterna;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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

    } catch (\Exception $e) {
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

            } catch (\Exception $e) {
                $errors[] = "Hoja $name: " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors'   => $errors,
        ];
    }

    // ==================== EXTRACT ====================
    private function extractFromSheet($ws): array
    {
        return [
            'OF'                     => (int) ($ws->getCell('M3')->getValue() ?? 0),
            'NumeroCisterna'         => (int) ($ws->getCell('M2')->getValue() ?? 0),
            'Conductor'              => trim((string) ($ws->getCell('H16')->getValue() ?? '')),
            'Telefono'               => trim((string) ($ws->getCell('H17')->getValue() ?? '')),
            'Origen'                 => trim((string) ($ws->getCell('M9')->getValue() ?? '')),
            'Destino'                => trim((string) ($ws->getCell('M10')->getValue() ?? '')),
            'Matricula'              => trim((string) ($ws->getCell('M5')->getValue() ?? '')),
            'MatriculaCisterna'      => trim((string) ($ws->getCell('M6')->getValue() ?? '')),
            'Transporte'             => trim((string) ($ws->getCell('M7')->getValue() ?? '')),
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
            'GlobalGAP'              => null,
            'FDA'                    => null,
            'Observaciones'          => implode(' | ', array_filter([
                $ws->getCell('C14')->getValue()
                    ? 'Concepto: ' . trim($ws->getCell('C14')->getValue())
                    : null,
                $ws->getCell('H14')->getValue()
                    ? 'BRIX: ' . trim($ws->getCell('H14')->getValue())
                    : null,
                $ws->getCell('L14')->getValue()
                    ? 'Kilos: ' . $ws->getCell('L14')->getCalculatedValue()
                    : null,
                $ws->getCell('D15')->getValue()
                    ? 'Precintos: ' . trim($ws->getCell('D15')->getValue())
                    : null,
                $ws->getCell('J15')->getValue()
                    ? 'Tara: ' . $ws->getCell('J15')->getValue()
                    : null,
            ])),
            'Incidencias' => '',
        ];
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            $dt = Date::excelToDateTimeObject($value);
            return $dt->format('Y-m-d H:i:s');
        }

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }

        try {
            return (new \DateTime((string)$value))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime($dateValue, $timeValue): ?string
    {
        if ($dateValue === null) return null;

        if (is_numeric($dateValue)) {
            $date = Date::excelToDateTimeObject($dateValue);
        } elseif ($dateValue instanceof \DateTime) {
            $date = $dateValue;
        } else {
            try {
                $date = new \DateTime((string)$dateValue);
            } catch (\Exception $e) {
                return null;
            }
        }

        if ($timeValue !== null && $timeValue !== '') {
            if (is_numeric($timeValue)) {
                $time = Date::excelToDateTimeObject($timeValue);
                $date->setTime(
                    (int)$time->format('H'),
                    (int)$time->format('i')
                );
            } elseif ($timeValue instanceof \DateTime) {
                $date->setTime(
                    (int)$timeValue->format('H'),
                    (int)$timeValue->format('i')
                );
            }
        }

        return $date->format('Y-m-d H:i:s');
    }
}