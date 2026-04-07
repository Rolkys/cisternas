<?php

/**
 * DOC: Proyecto Cisternas
 * Archivo personalizado del dominio de negocio.
 * Contiene logica especifica de gestion de cisternas/usuarios/planificacion.
 */

namespace App\Http\Controllers;

use App\Models\Cisterna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CisternaController extends Controller
{
    // ==================== INDEX ====================
    /**
     * Muestra el listado principal de registros.
     */
    public function index(Request $request)
    {
        $query = Cisterna::query();
        $year = $request->input('year');

        if ($request->filled('year')) {
            $query->where(function ($q) use ($year) {
                $q->whereYear('FechaConsumoMG', $year)
                    ->orWhere(function ($q2) use ($year) {
                        $q2->whereNull('FechaConsumoMG')
                            ->whereYear('FechaEntradaMG', $year);
                    });
            });
        } else {
            // Filtro automatico por year: muestra el year actual + diciembre del year anterior
            $yearActual = now()->year;
            $query->where(function ($q) use ($yearActual) {
                $q->whereYear('FechaConsumoMG', $yearActual)
                    ->orWhere(function ($q2) use ($yearActual) {
                        $q2->whereYear('FechaConsumoMG', $yearActual - 1)
                            ->whereMonth('FechaConsumoMG', 12);
                    })
                    // Tambien incluir por FechaEntradaMG si no hay FechaConsumoMG
                    ->orWhere(function ($q3) use ($yearActual) {
                        $q3->whereNull('FechaConsumoMG')
                            ->whereYear('FechaEntradaMG', $yearActual);
                    })
                    ->orWhere(function ($q4) use ($yearActual) {
                        $q4->whereNull('FechaConsumoMG')
                            ->whereYear('FechaEntradaMG', $yearActual - 1)
                            ->whereMonth('FechaEntradaMG', 12);
                    })
                    ->orWhere(function ($q5) {
                        $q5->whereNull('FechaConsumoMG')
                            ->whereNull('FechaEntradaMG');
                    });
            });
        }

        // Filtro por texto
        if ($request->filled('texto')) {
            $texto = $request->texto;
            $query->where(function ($q) use ($texto) {
                $q->where('conductor', 'like', "%$texto%")
                    ->orWhere('MatriculaCisterna', 'LIKE', "%{$texto}%")
                    ->orWhere('origen', 'like', "%$texto%")
                    ->orWhere('destino', 'like', "%$texto%");
            });
        }

        // Filtro por fecha de consumo (tambien busca en FechaEntradaMG si no hay FechaConsumoMG)
        if ($request->filled('fecha')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('FechaConsumoMG', $request->fecha)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('FechaConsumoMG')
                            ->whereDate('FechaEntradaMG', $request->fecha);
                    });
            });
        }

        $cisternas = $query->orderByDesc('numeroCisterna')->paginate(30);

        return view('cisterna.index', compact('cisternas'));
    }
    // ==================== CREATE ====================
    /**
     * Muestra el formulario para crear un nuevo registro.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin() && !$user->isUser()) {
            abort(403, 'No tienes permisos para crear registros');
        }

        return view('cisterna.create');
    }

    // ==================== STORE ====================
    /**
     * Valida la solicitud y crea un nuevo registro.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin() && !$user->isUser()) {
            abort(403, 'No tienes permisos para crear registros');
        }

        $request->validate([
            'OF'                     => 'required|integer',
            'NumeroCisterna'         => 'required|integer',
            'Conductor'              => 'required|string|max:255',
            'Origen'                 => 'nullable|string|max:255',
            'Destino'                => 'nullable|string|max:255',
            'Matricula'              => 'nullable|string|max:255',
            'MatriculaCisterna'      => 'nullable|string|max:255',
            'Telefono'               => 'nullable|string|max:255',
            'Transporte'             => 'nullable|string|max:255',
            'FechaConsumoMG'         => 'nullable|date',
            'Observaciones'          => 'nullable|string',
            'Incidencias'            => 'nullable|string',
            'GlobalGAP'              => 'nullable|boolean',
            'FDA'                    => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data = $this->syncFechasConsumoEntrada($data);

        $data = $this->autoConsumir($data);

        Cisterna::create($data);

        return redirect()->route('cisterna.index')
                        ->with('success', '✅ Cisterna creada exitosamente.');
    }

    // ==================== SHOW ====================
    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show(Cisterna $cisterna)
    {
        return view('cisterna.show', compact('cisterna'));
    }

    // ==================== EDIT ====================
    /**
     * Muestra el formulario para editar un registro.
     */
    public function edit(Cisterna $cisterna)
    {
        return view('cisterna.edit', compact('cisterna'));
    }

    // ==================== UPDATE ====================
    /**
     * Valida la solicitud y actualiza un registro existente.
     */
    public function update(Request $request, Cisterna $cisterna)
    {
        $user = Auth::user();

        // Root y Admin pueden editar todo
        if ($user->isRoot() || $user->isAdmin()) {
            $request->validate([
                'OF'             => 'required|integer',
                'NumeroCisterna' => 'required|integer',
                'Conductor'      => 'required|string|max:255',
                'Origen'         => 'nullable|string|max:255',
                'Destino'        => 'nullable|string|max:255',
                'Matricula'      => 'nullable|string|max:50',
                'MatriculaCisterna' => 'nullable|string|max:50',
                'Telefono'       => 'nullable|string|max:20',
                'Transporte'     => 'nullable|string|max:255',
                'FechaConsumoMG' => 'nullable|date',
                'Observaciones'  => 'nullable|string',
                'Incidencias'    => 'nullable|string',
                'GlobalGAP'      => 'nullable|boolean',
                'FDA'            => 'nullable|boolean',
            ]);

            $data = $request->all();
            $data = $this->syncFechasConsumoEntrada($data);
            $data = $this->autoConsumir($data, $cisterna);
            $cisterna->update($data);

            return redirect()->route('cisterna.index')
                            ->with('success', '✅ Cisterna actualizada correctamente');
        }
        
        // User puede editar todos los campos
        if ($user->isUser()) {
            $request->validate([
                'OF'             => 'required|integer',
                'NumeroCisterna' => 'required|integer',
                'Conductor'      => 'required|string|max:255',
                'Origen'         => 'nullable|string|max:255',
                'Destino'        => 'nullable|string|max:255',
                'Matricula'      => 'nullable|string|max:50',
                'MatriculaCisterna' => 'nullable|string|max:50',
                'Telefono'       => 'nullable|string|max:20',
                'Transporte'     => 'nullable|string|max:255',
                'FechaConsumoMG' => 'nullable|date',
                'Observaciones'  => 'nullable|string',
                'Incidencias'    => 'nullable|string',
                'GlobalGAP'      => 'nullable|boolean',
                'FDA'            => 'nullable|boolean',
            ]);

            $data = $request->all();
            $data = $this->syncFechasConsumoEntrada($data);
            $data = $this->autoConsumir($data, $cisterna);
            $cisterna->update($data);

            return redirect()->route('cisterna.index')
                            ->with('success', '✅ Cisterna actualizada correctamente');
        }
        
        // Operario solo puede editar los campos de consumo y observaciones
        if ($user->isOperario()) {
            $request->validate([
                'HoraRealConsumoL1' => 'nullable|date_format:H:i',
                'HoraRealConsumoL2' => 'nullable|date_format:H:i',
                'Observaciones'     => 'nullable|string',
            ]);

            $base = $cisterna->FechaConsumoMG?->format('Y-m-d')
                 ?? $cisterna->FechaEntradaMG?->format('Y-m-d')
                 ?? now()->format('Y-m-d');

            if ($request->has('HoraRealConsumoL1')) {
                $cisterna->HoraRealConsumoL1 = $request->HoraRealConsumoL1
                    ? $base . ' ' . $request->HoraRealConsumoL1 . ':00'
                    : null;
            }

            if ($request->has('HoraRealConsumoL2')) {
                $cisterna->HoraRealConsumoL2 = $request->HoraRealConsumoL2
                    ? $base . ' ' . $request->HoraRealConsumoL2 . ':00'
                    : null;
            }

            if ($request->has('Observaciones')) {
                $cisterna->Observaciones = $request->Observaciones;
            }
            
            $cisterna->save();

            return redirect()->route('cisterna.index')
                            ->with('success', '✅ Consumo actualizado correctamente');
        }
        
        abort(403, 'No tienes permisos para editar');
    }

    //==================== DESTROY ====================
    /**
     * Elimina un registro del sistema.
     */
    public function destroy(Cisterna $cisterna)
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para eliminar registros');
        }

        if ($cisterna->Observaciones || $cisterna->Incidencias) {
            session()->flash('deleted_observaciones', $cisterna->Observaciones);
            session()->flash('deleted_incidencias', $cisterna->Incidencias);
            session()->flash('deleted_numero', $cisterna->NumeroCisterna);
        }

        $cisterna->delete();

        return redirect()->route('cisterna.index')
                        ->with('success', '✅ Cisterna eliminada correctamente.');
    }

    // ==================== UPDATE CONSUMO MODAL ====================
    /**
     * Actualiza los datos de consumo de una cisterna.
     */
    public function updateConsumo(Request $request, Cisterna $cisterna)
    {
        $user = Auth::user();
        
        $request->validate([
            'HoraRealConsumoL1'  => 'nullable|date_format:H:i',
            'HoraRealConsumoL2'  => 'nullable|date_format:H:i',
            'Observaciones'      => 'nullable|string'
        ]);

        // CAMBIO 1: Fallback a FechaEntradaMG si no hay FechaConsumoMG
        $base = $cisterna->FechaConsumoMG?->format('Y-m-d')
             ?? $cisterna->FechaEntradaMG?->format('Y-m-d')
             ?? now()->format('Y-m-d');

        if ($user->isOperario()) {
            $cisterna->HoraRealConsumoL1 = $request->HoraRealConsumoL1
                ? $base . ' ' . $request->HoraRealConsumoL1 . ':00'
                : $cisterna->HoraRealConsumoL1;

            $cisterna->HoraRealConsumoL2 = $request->HoraRealConsumoL2
                ? $base . ' ' . $request->HoraRealConsumoL2 . ':00'
                : $cisterna->HoraRealConsumoL2;

            $cisterna->Observaciones = $request->Observaciones ?? $cisterna->Observaciones;
        } else {
            $cisterna->HoraRealConsumoL1 = $request->HoraRealConsumoL1
                ? $base . ' ' . $request->HoraRealConsumoL1 . ':00'
                : null;

            $cisterna->HoraRealConsumoL2 = $request->HoraRealConsumoL2
                ? $base . ' ' . $request->HoraRealConsumoL2 . ':00'
                : null;

            $cisterna->Observaciones = $request->Observaciones;
        }
        
        $cisterna->save();

        return redirect()->route('cisterna.index')
                        ->with('success', '✅ Consumo Actualizado Correctamente');
    }

    // ==================== BULK UPLOAD ====================
    /**
     * Muestra la vista para iniciar una carga masiva.
     */
    public function bulkUpload()
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para realizar carga masiva');
        }
        
        return view('cisterna.bulk');
    }

    /**
     * Valida y guarda temporalmente el archivo de carga masiva.
     */
    public function bulkStore(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para realizar carga masiva');
        }
        
        $request->validate([
            'excel' => 'required|file|mimes:xlsx,xls',
        ]);

        $path     = $request->file('excel')->store('temp');
        $fullPath = storage_path('app/private/' . $path);

        $service = new \App\Services\ExcelImportService();
        $preview = $service->preview($fullPath, true);

        session([
            'bulk_preview'  => $preview,
            'bulk_tempPath' => $path,
        ]);

        return redirect()->route('cisterna.bulk.confirm');
    }

    /**
     * Muestra la previsualizacion antes de confirmar la importacion.
     */
    public function bulkConfirm()
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para realizar carga masiva');
        }
        
        $preview = session('bulk_preview');

        if (!$preview) {
            return redirect()->route('cisterna.bulk')
                                ->with('error', '❌ No hay datos pendientes de confirmar.');
        }

        return view('cisterna.bulk_confirm', compact('preview'));
    }

    /**
     * Procesa la importacion masiva y guarda los registros confirmados.
     */
    public function bulkConfirmStore(Request $request)
    {
        $user = Auth::user();
 
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para realizar carga masiva');
        }
 
        $tempPath = session('bulk_tempPath');
        $filas    = $request->input('filas', []);
        $previewSession = session('bulk_preview', []);
        $isImportAll = $request->boolean('import_all');
        $hasEditedRowsJson = $request->filled('edited_rows_json');
 
        $imported = 0;
        $omitidos = 0;
        $actualizados = 0;

        // Evita límites de max_input_vars en formularios grandes:
        // si se pulsa "importar todas", se lee directamente el Excel temporal.
        $postTruncado = !empty($previewSession)
            && (
                (count($filas) > 0 && count($filas) < count($previewSession))
                || ($hasEditedRowsJson && count($filas) !== count($previewSession))
            );
        if ($isImportAll || $postTruncado) {
            if (!$tempPath) {
                return redirect()->route('cisterna.bulk')
                    ->with('error', 'No hay archivo temporal para importar.');
            }

            $fullPath = storage_path('app/private/' . $tempPath);
            $service = new \App\Services\ExcelImportService();
            $preview = $service->preview($fullPath, true);
            $editedRows = json_decode((string) $request->input('edited_rows_json', '[]'), true);
            if (!is_array($editedRows)) {
                $editedRows = [];
            }

            foreach ($preview as $index => $fila) {
                $filaEditada = $fila;
                if (isset($editedRows[(string) $index]) && is_array($editedRows[(string) $index])) {
                    $filaEditada = array_merge($filaEditada, $editedRows[(string) $index]);
                } elseif (isset($editedRows[$index]) && is_array($editedRows[$index])) {
                    $filaEditada = array_merge($filaEditada, $editedRows[$index]);
                }

                if (!$isImportAll && empty($filaEditada['_incluir'])) {
                    $omitidos++;
                    continue;
                }

                if (!empty($filaEditada['_error'])) {
                    $omitidos++;
                    continue;
                }

                $data = collect($filaEditada)->except(['_incluir', '_hoja', '_error'])->toArray();

                $data['GlobalGAP'] = !empty($filaEditada['GlobalGAP']);
                $data['FDA'] = !empty($filaEditada['FDA']);

                if (isset($data['Observaciones']) && trim((string) $data['Observaciones']) === '') {
                    $data['Observaciones'] = null;
                }

                // En importacion, conservar H.E.C si el usuario la edita en bulk-confirm.
                if (array_key_exists('HoraEstimadaConsumoL1', $data) && trim((string) $data['HoraEstimadaConsumoL1']) === '') {
                    $data['HoraEstimadaConsumoL1'] = null;
                }
                if (array_key_exists('HoraEstimadaConsumoL2', $data) && trim((string) $data['HoraEstimadaConsumoL2']) === '') {
                    $data['HoraEstimadaConsumoL2'] = null;
                }
                $data = $this->marcarConsumidaSiTamariteEnImport($data);

                $data = $this->syncFechasConsumoEntrada($data);
                $data = $this->autoConsumir($data, null, true);

                $existing = Cisterna::where('OF', $data['OF'] ?? null)
                    ->where('NumeroCisterna', $data['NumeroCisterna'] ?? null)
                    ->first();

                if ($existing) {
                    if ($isImportAll) {
                        $existing->update($data);
                        $actualizados++;
                    } else {
                        $omitidos++;
                    }
                } else {
                    Cisterna::create($data);
                    $imported++;
                }
            }

            if ($tempPath) {
                \Storage::delete($tempPath);
            }

            session()->forget(['bulk_preview', 'bulk_tempPath']);

            if (!$isImportAll) {
                return redirect()->route('cisterna.index')
                    ->with('success', "{$imported} cisternas importadas. {$omitidos} omitidas.");
            }


            return redirect()->route('cisterna.index')
                ->with('success', "✅ {$imported} creadas, {$actualizados} actualizadas, {$omitidos} omitidas.");
        }
 
        foreach ($filas as $fila) {
 
            if (empty($fila['_incluir'])) {
                $omitidos++;
                continue;
            }
 
            $existe = Cisterna::where('OF', $fila['OF'])
                                ->where('NumeroCisterna', $fila['NumeroCisterna'])
                                ->exists();
    
            if ($existe) {
                $omitidos++;
                continue;
            }

            $data = collect($fila)->except(['_incluir', '_hoja'])->toArray();
 
            $data['GlobalGAP'] = isset($fila['GlobalGAP']) ? (bool) $fila['GlobalGAP'] : false;
            $data['FDA']       = isset($fila['FDA'])       ? (bool) $fila['FDA']       : false;
 
            if (isset($data['Observaciones']) && trim($data['Observaciones']) === '') {
                $data['Observaciones'] = null;
            }

            // En importacion, conservar H.E.C si el usuario la edita en bulk-confirm.
            if (array_key_exists('HoraEstimadaConsumoL1', $data) && trim((string) $data['HoraEstimadaConsumoL1']) === '') {
                $data['HoraEstimadaConsumoL1'] = null;
            }
            if (array_key_exists('HoraEstimadaConsumoL2', $data) && trim((string) $data['HoraEstimadaConsumoL2']) === '') {
                $data['HoraEstimadaConsumoL2'] = null;
            }
            $data = $this->marcarConsumidaSiTamariteEnImport($data);

            $data = $this->syncFechasConsumoEntrada($data);
            $data = $this->autoConsumir($data, null, true);

            Cisterna::create($data);
            $imported++;
        }
 
        if ($tempPath) {
            \Storage::delete($tempPath);
        }
 
        session()->forget(['bulk_preview', 'bulk_tempPath']);
 
        return redirect()->route('cisterna.index')
                            ->with('success', "✅ {$imported} cisternas importadas. {$omitidos} omitidas.");
    }

    // ==================== EXPORTAR EXCEL ====================
    /**
     * Exporta los datos filtrados a un archivo Excel.
     */
    public function export(Request $request)
    {
        $query = Cisterna::query();

        if($request->filled('texto')){
            $texto = $request->texto;
            $query->where(function ($q) use ($texto){
                $q->where('Conductor', 'like', "%$texto%")
                    ->orWhere('Matricula', 'like', "%$texto%")
                    ->orWhere('Origen', 'like', "%$texto%")
                    ->orWhere('Destino', 'like', "%$texto%");
            });
        }

        if($request->filled('fecha')){
            $query->whereDate('FechaConsumoMG', $request->fecha);
        }

        $cisternas = $query->orderByDesc('NumeroCisterna')->get();

        $service = new \App\Services\ExcelExportService();
        $filePath = $service->export($cisternas);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    // ==================== DASHBOARD ====================
    /**
     * Calcula metricas y datos para el panel de control.
     */
    public function dashboard(Request $request)
    {
        $desde = $request->filled('desde') ? $request->desde : null;
        $hasta = $request->filled('hasta') ? $request->hasta : null;

        $query = Cisterna::query();

        if ($desde || $hasta) {
            $query->where(function ($q) use ($desde, $hasta) {
                if ($desde) {
                    $q->where(function ($sub) use ($desde) {
                        $sub->whereDate('FechaConsumoMG', '>=', $desde)
                            ->orWhereDate('FechaEntradaMG', '>=', $desde);
                    });
                }
                if ($hasta) {
                    $q->where(function ($sub) use ($hasta) {
                        $sub->whereDate('FechaConsumoMG', '<=', $hasta)
                            ->orWhereDate('FechaEntradaMG', '<=', $hasta);
                    });
                }
            });
        }

        $total          = (clone $query)->count();
        $consumidas     = (clone $query)->whereNotNull('HoraRealConsumoL1')->count();
        $pendientes     = (clone $query)->whereNull('HoraRealConsumoL1')
                                        ->whereNull('Incidencias')
                                        ->count();
        
        $incidencias    = (clone $query)->whereNotNull('Incidencias')
                                        ->where('Incidencias', '!=', '')
                                        ->count();

        $hoy_count      = Cisterna::whereDate('FechaConsumoMG', today())->count();
        
        $en_transito    = Cisterna::whereNull('FechaEntradaMG')
                                    ->whereNull('HoraRealConsumoL1')
                                    ->count();

        $recientes      = Cisterna::orderByDesc('IdCisterna')->take(5)->get();

        $hoy_cisternas  = Cisterna::whereDate('FechaConsumoMG', today())
                                    ->orderBy('HoraEstimaadConsumoL1')
                                    ->get();

        $años = Cisterna::selectRaw('strftime("%Y", COALESCE(FechaConsumoMG, created_at)) as ano')
                        ->groupBy('ano')
                        ->orderByDesc('ano')
                        ->pluck('ano');

        $añoSeleccionado = $request->año;
        $cisternasDelAño = collect();

        if ($añoSeleccionado) {
            $cisternasDelAño = Cisterna::where(function ($q) use ($añoSeleccionado) {
                $q->whereYear('FechaConsumoMG', $añoSeleccionado)
                ->orWhere(function ($q2) use ($añoSeleccionado) {
                    $q2->whereNull('FechaConsumoMG')
                        ->whereYear('created_at', $añoSeleccionado);
                });
            })
            ->orderByDesc('NumeroCisterna')
            ->get();
        }

        return view('cisterna.dashboard', compact(
            'total', 'consumidas', 'hoy_count', 'incidencias',
            'pendientes', 'en_transito', 'recientes', 'hoy_cisternas',
            'años', 'añoSeleccionado', 'cisternasDelAño',
            'desde', 'hasta'
        ));
    }

    // ==================== HELPER: AUTO-CONSUMIR ====================
    /**
     * Completa automaticamente horas de consumo segun reglas de negocio.
     */
    private function autoConsumir(array $data, ?Cisterna $cisterna = null, bool $isImport = false): array
    {
        // En importacion no autocompletar horas desde ningun origen.
        if ($isImport) {
            return $data;
        }

        $destino = trim(strtolower((string) ($data['Destino'] ?? '')));

        $esMovatalla = str_contains($destino, 'moratalla') || $destino === '';

        if (!$esMovatalla) {
            $yaConsumaL1 = !empty($data['HoraRealConsumoL1'])
                || ($cisterna && $cisterna->HoraRealConsumoL1);
            $yaConsumaL2 = !empty($data['HoraRealConsumoL2'])
                || ($cisterna && $cisterna->HoraRealConsumoL2);

            if (!$yaConsumaL1 && !$yaConsumaL2) {
                // CAMBIO 1: Fallback a FechaEntradaMG si no hay FechaConsumoMG
                $fechaBase = !empty($data['FechaConsumoMG'])
                    ? \Carbon\Carbon::parse($data['FechaConsumoMG'])->format('Y-m-d')
                    : (!empty($data['FechaEntradaMG'])
                        ? \Carbon\Carbon::parse($data['FechaEntradaMG'])->format('Y-m-d')
                        : now()->format('Y-m-d'));

                if (!empty($data['HoraEstimadaConsumoL1'])) {
                    $hora = strlen($data['HoraEstimadaConsumoL1']) <= 5
                        ? $data['HoraEstimadaConsumoL1']
                        : \Carbon\Carbon::parse($data['HoraEstimadaConsumoL1'])->format('H:i');
                    $data['HoraRealConsumoL1'] = $fechaBase . ' ' . $hora . ':00';
                } elseif (!empty($data['HoraEstimadaConsumoL2'])) {
                    $hora = strlen($data['HoraEstimadaConsumoL2']) <= 5
                        ? $data['HoraEstimadaConsumoL2']
                        : \Carbon\Carbon::parse($data['HoraEstimadaConsumoL2'])->format('H:i');
                    $data['HoraRealConsumoL2'] = $fechaBase . ' ' . $hora . ':00';
                } else {
                    $data['HoraRealConsumoL1'] = $fechaBase . ' ' . now()->format('H:i') . ':00';
                }
            }
        }

        return $data;
    }

    /**
     * En importacion, si destino es TAMARITE DE LITERA se marca como consumida.
     */
    private function marcarConsumidaSiTamariteEnImport(array $data): array
    {
        $destino = (string) ($data['Destino'] ?? '');
        if (stripos($destino, 'tamarite de litera') === false) {
            return $data;
        }

        $fechaBase = !empty($data['FechaConsumoMG'])
            ? \Carbon\Carbon::parse($data['FechaConsumoMG'])->format('Y-m-d')
            : (!empty($data['FechaEntradaMG'])
                ? \Carbon\Carbon::parse($data['FechaEntradaMG'])->format('Y-m-d')
                : now()->format('Y-m-d'));

        $data['HoraRealConsumoL1'] = $fechaBase . ' ' . now()->format('H:i') . ':00';
        $data['HoraRealConsumoL2'] = null;

        return $data;
    }

    /**
     * Sincroniza FechaEntradaMG para que coincida con FechaConsumoMG.
     */
    private function syncFechasConsumoEntrada(array $data): array
    {
        $fechaConsumo = $data['FechaConsumoMG'] ?? null;
        $data['FechaEntradaMG'] = $fechaConsumo ?: null;

        return $data;
    }

    /**
     * Elimina de forma masiva todos los registros de cisternas.
     */
    public function destroyAll()
    {
        if (!auth()->user()->isRoot() && !auth()->user()->isAdmin()) {
            abort(403, 'No autorizado');
        }
        
        \App\Models\Cisterna::truncate();
        
        return redirect()->route('cisterna.index')
            ->with('success', 'Todas las cisternas han sido eliminadas correctamente.');
    }
}


