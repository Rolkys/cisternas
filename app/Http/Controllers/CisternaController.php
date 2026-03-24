<?php

namespace App\Http\Controllers;

use App\Models\Cisterna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CisternaController extends Controller
{
    // ==================== INDEX ====================
    public function index(Request $request)
    {
        $query = Cisterna::query();

        // Filtro automático por año: muestra el año actual + diciembre del año anterior
        $añoActual = now()->year;
        $query->where(function($q) use ($añoActual) {
            $q->whereYear('FechaConsumoMG', $añoActual)
            ->orWhere(function($q2) use ($añoActual) {
                $q2->whereYear('FechaConsumoMG', $añoActual - 1)
                    ->whereMonth('FechaConsumoMG', 12);
            })
            ->orWhereNull('FechaConsumoMG');
        });

        // Filtro por texto
        if($request->filled('texto')){
            $texto = $request->texto;
            $query->where(function($q) use ($texto) {
                $q->where('conductor', 'like', "%$texto%")
                    ->orWhere('matricula', 'like', "%$texto%")
                    ->orWhere('origen', 'like', "%$texto%")
                    ->orWhere('destino', 'like', "%$texto%");
            });
        }

        // Filtro por fecha de consumo
        if($request->filled('fecha')){
            $query->whereDate('FechaConsumoMG', $request->fecha);
        }

        $cisternas = $query->orderByDesc('numeroCisterna')->paginate(30);

        return view('cisterna.index', compact('cisternas'));
    }

    // ==================== CREATE ====================
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin() && !$user->isUser()) {
            abort(403, 'No tienes permisos para crear registros');
        }

        return view('cisterna.create');
    }

    // ==================== STORE ====================
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
            'FechaFabricacionHuelva' => 'nullable|date',
            'HoraSalida'             => 'nullable|date',
            'FechaEntradaMG'         => 'nullable|date',
            'Observaciones'          => 'nullable|string',
            'Incidencias'            => 'nullable|string',
            'GlobalGAP'              => 'nullable|boolean',
            'FDA'                    => 'nullable|boolean',
        ]);

        Cisterna::create($request->all());

        return redirect()->route('cisterna.index')
                        ->with('success', '✅ Cisterna creada exitosamente.');
    }

    // ==================== SHOW ====================
    public function show(Cisterna $cisterna)
    {
        return view('cisterna.show', compact('cisterna'));
    }

    // ==================== EDIT ====================
    public function edit(Cisterna $cisterna)
    {
        return view('cisterna.edit', compact('cisterna'));
    }

    // ==================== UPDATE ====================
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
                'FechaFabricacionHuelva' => 'nullable|date',
                'HoraSalida'     => 'nullable|date',
                'FechaEntradaMG' => 'nullable|date',
                'Observaciones'  => 'nullable|string',
                'Incidencias'    => 'nullable|string',
                'GlobalGAP'      => 'nullable|boolean',
                'FDA'            => 'nullable|boolean',
            ]);

            $cisterna->update($request->all());

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
                'FechaFabricacionHuelva' => 'nullable|date',
                'HoraSalida'     => 'nullable|date',
                'FechaEntradaMG' => 'nullable|date',
                'Observaciones'  => 'nullable|string',
                'Incidencias'    => 'nullable|string',
                'GlobalGAP'      => 'nullable|boolean',
                'FDA'            => 'nullable|boolean',
            ]);

            $cisterna->update($request->all());

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

            $base = $cisterna->FechaConsumoMG?->format('Y-m-d') ?? now()->format('Y-m-d');

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
    public function destroy(Cisterna $cisterna)
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para eliminar registros');
        }

        $cisterna->delete();

        return redirect()->route('cisterna.index')
                        ->with('success', '✅ Cisterna eliminada correctamente.');
    }

    // ==================== UPDATE CONSUMO MODAL ====================
    public function updateConsumo(Request $request, Cisterna $cisterna)
    {
        $user = Auth::user();
        
        $request->validate([
            'HoraRealConsumoL1'  => 'nullable|date_format:H:i',
            'HoraRealConsumoL2'  => 'nullable|date_format:H:i',
            'Observaciones'      => 'nullable|string'
        ]);

        $base = $cisterna->FechaConsumoMG?->format('Y-m-d') ?? now()->format('Y-m-d');

        // Si es operario, solo actualiza estos campos
        if ($user->isOperario()) {
            $cisterna->HoraRealConsumoL1 = $request->HoraRealConsumoL1
                ? $base . ' ' . $request->HoraRealConsumoL1 . ':00'
                : $cisterna->HoraRealConsumoL1;

            $cisterna->HoraRealConsumoL2 = $request->HoraRealConsumoL2
                ? $base . ' ' . $request->HoraRealConsumoL2 . ':00'
                : $cisterna->HoraRealConsumoL2;

            $cisterna->Observaciones = $request->Observaciones ?? $cisterna->Observaciones;
        } else {
            // Root, Admin y User pueden actualizar todos los campos
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
    public function bulkUpload()
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para realizar carga masiva');
        }
        
        return view('cisterna.bulk');
    }

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
        $preview = $service->preview($fullPath);

        session([
            'bulk_preview'  => $preview,
            'bulk_tempPath' => $path,
        ]);

        return redirect()->route('cisterna.bulk.confirm');
    }

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

    public function bulkConfirmStore(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isRoot() && !$user->isAdmin()) {
            abort(403, 'No tienes permisos para realizar carga masiva');
        }
        
        $tempPath   = session('bulk_tempPath');
        $filas      = $request->input('filas',[]);

        $imported = 0;
        $omitidos = 0;

        foreach($filas as $fila){
            if(empty($fila['_incluir'])){
                $omitidos++;
                continue;
            }

            $existe = Cisterna::where('OF', $fila['OF'])
                                ->where('NumeroCisterna', $fila['NumeroCisterna'])
                                ->exists();
            
            if($existe){
                $omitidos++;
                continue;
            }

            $data = collect($fila)->except(['_incluir', '_hoja'])->toArray();
            Cisterna::create($data);
            $imported++;
        }

        if($tempPath){
            \Storage::delete($tempPath);
        }

        session()->forget(['bulk_preview', 'bulk_tempPath']);

        return redirect()->route('cisterna.index')
                        ->with('success', "✅ {$imported} cisternas importadas. {$omitidos} omitidas.");
    }

    // ==================== EXPORTAR EXCEL ====================
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
}