<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDenunciaRequest;
use App\Http\Resources\DenunciaResource;
use App\Models\Denuncia;
use App\Services\DenunciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DenunciaController extends Controller
{
    private DenunciaService $denunciaService;

    public function __construct(DenunciaService $denunciaService)
    {
        $this->denunciaService = $denunciaService;
    }

    // POST /api/denuncias - Crear nueva denuncia
    public function store(StoreDenunciaRequest $request, DenunciaService $denunciaService)
    {
        $usuario_id = Auth::check() ? Auth::id() : null;
        $ip = $request->ip();
        $navegador = $request->header('User-Agent');

        $denuncia = $denunciaService->crearDenuncia(
            $request->validated(),
            $usuario_id,
            $ip,
            $navegador
        );

        return response()->json([
            'sucess' => true,
            'message' => 'Denuncia creada exitosamente',
            'data' => new DenunciaResource($denuncia),
            'acuse_recibo' => $this->generarAcuseRecibo($denuncia),
        ], 201);
    }

    // GET /api/denuncias/{folio} - Obtener detalles de una denuncia
    public function show($folio)
    {
        $denuncia = $this->denunciaService->obtenerDenuncia($folio);
        
        return response()->json([
            'success' => true,
            'data' => new DenunciaResource($denuncia),
        ]);
    }

    // GET /api/denuncias - Listar denuncias (usuario autenticado)
    public function index(Request $request)
    {
        $this->authorize('viewAny', Denuncia::class);

        $denuncias = Denuncia::when(Auth::check() && !Auth::user()->is_admin, function ($query){
            $query->where('usuario_id', Auth::id());
        })->paginate(15);

        return response()->json([
            'success' => true,
            'data' => DenunciaResource::collection($denuncias),
            'pagination' => [
                'total' => $denuncias->total(),
                'per_page' => $denuncias->perPage(),
                'current_page' => $denuncias->currentPage(),
                'last_page' => $denuncias->lastPage(),
            ]
        ]);
    }

    // Generar acuse de recibo

    private function generarAcuseRecibo(Denuncia $denuncia)
    {
        return [
            'folio' => $denuncia->folio,
            'fecha_recibida' => $denuncia->fecha_recibida->format('Y-m-d H:i:s'),
            'titulo_denuncia' => $denuncia->estado,
            'estado' => $denuncia->estado,
            'proximos_pasos' => [
                '1. Tu denuncia ha sido registrada en el sistema',
                '2. Recibirás actualizaciones en el correo proporcionado',
                '3. Guarda tu folio para consultas posteriores',
                '4. El proceso de revisión puede tomar entre 5-10 días hábiles',
            ]

        ];
    }
}