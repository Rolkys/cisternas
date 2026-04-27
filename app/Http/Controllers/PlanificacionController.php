<?php

/**
 * DOC: Proyecto Cisternas
 * Archivo personalizado del dominio de negocio.
 * Contiene logica especifica de gestion de cisternas/usuarios/planificacion.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlanificacionController extends Controller
{
    // Ruta del archivo JSON
    private $jsonPath;

    /**
     * Inicializa propiedades y dependencias del componente.
     */
    public function __construct()
    {
        $this->jsonPath = storage_path('app/planificacion.json');
    }

    // ==================== HELPERS JSON ====================
    /**
     * Lee y devuelve las filas guardadas en el JSON de planificacion.
     */
    private function leer(): array
    {
        if (!file_exists($this->jsonPath)) return [];
        $contenido = file_get_contents($this->jsonPath);
        return json_decode($contenido, true) ?? [];
    }

    /**
     * Guarda en disco las filas de planificacion en formato JSON.
     */
    private function guardar(array $filas): void
    {
        file_put_contents($this->jsonPath, json_encode($filas, JSON_PRETTY_PRINT));
    }

    // ==================== INDEX ====================
    /**
     * Muestra el listado principal de registros.
     */
    public function index()
    {
        $filas = $this->leer();
        return view('planificacion.index', compact('filas'));
    }

    // ==================== STORE ====================
    /**
     * Valida la solicitud y crea un nuevo registro.
     */
    public function store(Request $request)
    {
        $this->soloAdmin();

        $request->validate([
            'NumeroCisterna'         => 'required|integer',
            'Destino'                => 'nullable|string|max:255',
            'FechaConsumo'           => 'nullable|date',
            'FechaFabricacionHuelva' => 'nullable|date',
            'HoraEstimadaConsumoL1'  => 'nullable|date_format:H:i',
            'HoraEstimadaConsumoL2'  => 'nullable|date_format:H:i',
        ]);

        $filas   = $this->leer();
        $filas[] = [
            'id'                     => uniqid(),
            'NumeroCisterna'         => $request->NumeroCisterna,
            'Destino'                => $request->Destino,
            'FechaConsumo'           => $request->FechaConsumo,
            'FechaFabricacionHuelva' => $request->FechaFabricacionHuelva,
            'HoraEstimadaConsumoL1'  => $request->HoraEstimadaConsumoL1,
            'HoraEstimadaConsumoL2'  => $request->HoraEstimadaConsumoL2,
        ];

        $this->guardar($filas);

        return redirect()->route('planificacion.index')
                         ->with('success', '✅ Fila añadida.');
    }

    // ==================== EDIT ====================
    /**
     * Muestra el formulario para editar un registro.
     */
    public function edit(string $id)
    {
        $this->soloAdmin();
        $filas = $this->leer();
        $fila  = collect($filas)->firstWhere('id', $id);
        if (!$fila) abort(404);
        return view('planificacion.edit', compact('fila'));
    }

    // ==================== UPDATE ====================
    /**
     * Valida la solicitud y actualiza un registro existente.
     */
    public function update(Request $request, string $id)
    {
        $this->soloAdmin();

        $request->validate([
            'NumeroCisterna'         => 'required|integer',
            'Destino'                => 'nullable|string|max:255',
            'FechaConsumo'           => 'nullable|date',
            'FechaFabricacionHuelva' => 'nullable|date',
            'HoraEstimadaConsumoL1'  => 'nullable|date_format:H:i',
            'HoraEstimadaConsumoL2'  => 'nullable|date_format:H:i',
        ]);

        $filas = $this->leer();
        $filas = array_map(function($f) use ($request, $id) {
            if ($f['id'] === $id) {
                return [
                    'id'                     => $id,
                    'NumeroCisterna'         => $request->NumeroCisterna,
                    'Destino'                => $request->Destino,
                    'FechaConsumo'           => $request->FechaConsumo,
                    'FechaFabricacionHuelva' => $request->FechaFabricacionHuelva,
                    'HoraEstimadaConsumoL1'  => $request->HoraEstimadaConsumoL1,
                    'HoraEstimadaConsumoL2'  => $request->HoraEstimadaConsumoL2,
                ];
            }
            return $f;
        }, $filas);

        $this->guardar($filas);

        return redirect()->route('planificacion.index')
                         ->with('success', '✅ Fila actualizada.');
    }

    // ==================== DESTROY ====================
    /**
     * Elimina un registro del sistema.
     */
    public function destroy(string $id)
    {
        $this->soloAdmin();
        $filas = $this->leer();
        $filas = array_values(array_filter($filas, function ($f) use ($id) {
            return $f['id'] !== $id;
        }));
        $this->guardar($filas);
        return redirect()->route('planificacion.index')
                         ->with('success', '✅ Fila eliminada.');
    }

    // ==================== LIMPIAR TODO ====================
    /**
     * Limpia todos los datos de planificacion almacenados.
     */
    public function clear()
    {
        $this->soloAdmin();
        $this->guardar([]);
        return redirect()->route('planificacion.index')
                         ->with('success', '✅ Planificación limpiada.');
    }

    // ==================== EXPORTAR ====================
    /**
     * Genera y descarga el Excel de planificacion.
     */
    public function exportar()
    {
        $filas = $this->leer();

        if (empty($filas)) {
            return redirect()->route('planificacion.index')
                             ->with('error', '❌ No hay filas para exportar.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Planificación');

        $sheet->setCellValue('A1', 'Nº Cisterna');
        $sheet->setCellValue('B1', 'Destino');
        $sheet->setCellValue('C1', 'Fecha Consumo');
        $sheet->setCellValue('D1', 'Fecha Fab. Huelva');
        $sheet->setCellValue('E1', 'H.E.C L1');
        $sheet->setCellValue('F1', 'H.E.C L2');

        $headerStyle = $sheet->getStyle('A1:F1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFont()->getColor()->setARGB('FFFFFFFF');
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setARGB('FF0F2130');
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 2;
        foreach ($filas as $fila) {
            $sheet->setCellValue('A' . $row, str_pad($fila['NumeroCisterna'], 4, '0', STR_PAD_LEFT));
            $sheet->setCellValue('B' . $row, $fila['Destino'] ?? '');
            $sheet->setCellValue('C' . $row, $fila['FechaConsumo'] ?? '');
            $sheet->setCellValue('D' . $row, $fila['FechaFabricacionHuelva'] ?? '');
            $sheet->setCellValue('E' . $row, $fila['HoraEstimadaConsumoL1'] ?? '');
            $sheet->setCellValue('F' . $row, $fila['HoraEstimadaConsumoL2'] ?? '');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'planificacion_' . now()->format('Y-m-d_H-i') . '.xlsx';
        $tempPath = storage_path('app/private/temp/' . $filename);

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    // ==================== HELPER ====================
    /**
     * Verifica que el usuario autenticado tenga permisos de administrador.
     */
    private function soloAdmin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Solo administradores pueden modificar la planificación.');
        }
    }
}

