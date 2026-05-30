<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDenunciaRequest;
use App\Http\Resources\DenunciaResource;
use App\Models\Denuncia;
use App\Models\DenunciaArchivo;
use App\Services\ContrasenaAccesoService;
use App\Services\DenunciaArchivoService;
use App\Services\DenunciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DenunciaController extends Controller
{
    private DenunciaService $denunciaService;
    private DenunciaArchivoService $archivoService;
    private ContrasenaAccesoService $contrasenaService;

    public function __construct(
        DenunciaService $denunciaService,
        DenunciaArchivoService $archivoService,
        ContrasenaAccesoService $contrasenaService
    ) {
        $this->denunciaService = $denunciaService;
        $this->archivoService = $archivoService;
        $this->contrasenaService = $contrasenaService;
    }

    // POST /api/denuncias - Crear nueva denuncia
    public function store(StoreDenunciaRequest $request)
    {
        $usuario_id = Auth::check() ? Auth::id() : null;
        $ip = $request->ip();
        $navegador = $request->header('User-Agent');

        $resultado = $this->denunciaService->crearDenuncia(
            $request->validated(),
            $usuario_id,
            $ip,
            $navegador
        );

        $denuncia = $resultado['denuncia'];
        $contrasena = $resultado['contrasena'];

        return response()->json([
            'success' => true,
            'message' => 'Denuncia creada exitosamente',
            'data' => new DenunciaResource($denuncia),
            'acuse_recibo' => $this->generarAcuseRecibo($denuncia, $contrasena),
        ], 201);
    }

    // POST /api/denuncias/consultar - Consultar denuncia con contraseña de acceso
    public function consultarConContrasena(Request $request)
    {
        $request->validate([
            'folio' => 'required|string',
            'contrasena_acceso' => 'required|string',
        ]);

        $denuncia = Denuncia::where('folio', $request->input('folio'))->first();

        if (!$denuncia) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró una denuncia con ese folio',
            ], 404);
        }

        if (!$denuncia->contrasena_acceso) {
            return response()->json([
                'success' => false,
                'message' => 'Esta denuncia no tiene contraseña de acceso configurada',
            ], 422);
        }

        if (!$this->contrasenaService->verificar($request->input('contrasena_acceso'), $denuncia->contrasena_acceso)) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña de acceso incorrecta',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => new DenunciaResource($denuncia),
        ]);
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

        $denuncias = Denuncia::when(Auth::check() && !Auth::user()->is_admin, function ($query) {
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

    // POST /api/denuncias/{folio}/archivos - Subir archivo
    public function subirArchivo(Request $request, $folio)
    {
        $denuncia = $this->denunciaService->obtenerDenuncia($folio);

        $request->validate([
            'archivo' => 'required|file|max:10240'
        ]);

        try {
            $archivo = $this->archivoService->guardarArchivo(
                $denuncia,
                $request->file('archivo'),
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Archivo cargado exitosamente',
                'data' => $archivo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // GET /api/denuncias/{folio}/archivos - Obtener archivos
    public function obtenerArchivos($folio)
    {
        $denuncia = $this->denunciaService->obtenerDenuncia($folio);
        $archivos = $this->archivoService->obtenerArchivos($denuncia);

        return response()->json([
            'success' => true,
            'data' => $archivos
        ]);
    }

    // GET /api/denuncias/archivos/{id}/descargar - Descargar archivo
    public function descargarArchivo(DenunciaArchivo $archivo)
    {
        try {
            return $this->archivoService->descargarArchivo($archivo);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    // GET /api/denuncias/{folio}/bitacora - Obtener bitácora
    public function obtenerBitacora($folio)
    {
        $denuncia = $this->denunciaService->obtenerDenuncia($folio);
        $bitacora = $denuncia->bitacora()
            ->with('usuario:id,name,email')
            ->orderBy('fecha_accion', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bitacora
        ]);
    }

    // GET /api/denuncias/buscar - Buscar denuncias
    public function buscar(Request $request)
    {
        $this->authorize('viewAny', Denuncia::class);

        $query = Denuncia::query();

        if (Auth::check() && !Auth::user()->is_admin) {
            $query->where('usuario_id', Auth::id());
        }

        if ($request->has('folio')) {
            $query->where('folio', 'like', '%' . $request->input('folio') . '%');
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->has('prioridad')) {
            $query->where('prioridad', $request->input('prioridad'));
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_recibida', '>=', $request->input('fecha_desde'));
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_recibida', '<=', $request->input('fecha_hasta'));
        }

        if ($request->has('titulo')) {
            $query->where('titulo_denuncia', 'like', '%' . $request->input('titulo') . '%');
        }

        $denuncias = $query->orderBy('fecha_recibida', 'desc')->paginate(10);

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

    // PUT /api/denuncias/{folio}/estado - Cambiar estado (ADMIN)
    public function cambiarEstado(Request $request, $folio)
    {
        $this->authorize('update', Denuncia::class);

        $denuncia = $this->denunciaService->obtenerDenuncia($folio);

        $request->validate([
            'estado' => 'required|in:recibida,en_revision,en_proceso,resuelta,archivada',
            'descripcion' => 'required|string'
        ]);

        $denuncia = $this->denunciaService->actualizarEstado(
            $denuncia,
            $request->input('estado'),
            $request->input('descripcion'),
            Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado',
            'data' => new DenunciaResource($denuncia)
        ]);
    }

    private function generarAcuseRecibo(Denuncia $denuncia, string $contrasena): array
    {
        return [
            'folio' => $denuncia->folio,
            'fecha_recibida' => $denuncia->fecha_recibida->format('Y-m-d H:i:s'),
            'titulo_denuncia' => $denuncia->titulo_denuncia,
            'estado' => $denuncia->estado,
            'contrasena_acceso' => $contrasena,
            'proximos_pasos' => [
                'Tu denuncia ha sido registrada en el sistema',
                'Recibirás actualizaciones en el correo proporcionado',
                'Guarda tu folio y contraseña de acceso para consultas posteriores',
                'El proceso de revisión puede tomar entre 5-10 días hábiles',
            ],
        ];
    }
}
