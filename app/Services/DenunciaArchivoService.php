<?php

namespace App\Services;

use App\Models\Denuncia;
use App\Models\DenunciaArchivo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DenunciaArchivoService
{
    private $diskName = 'denuncias_archivos';
    private $maxFileSize = 10 * 1024 * 1024;
    private $extensionesPermitidas = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'pdf',
        'doc', 'docx', 'xsl', 'xlsx', 'txt',
    ];

    public function validarArchivo(UploadedFile $file)
    {
        //validar tamaño
        if ($file->getSize() > $this->maxFileSize) {
            throw new \Exception('El archivo excede el tamaño máximo permitido (10MB)');
        }

        //validar extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->extensionesPermitidas)) {
            throw new \Exception('Tipo de archivo no permitido');
        }

        // Validar MIME type
        $mimeType = $file->getMimeType();
        $mimePermitidos = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain'
        ];

        if (!in_array($mimeType, $mimePermitidos)) {
            throw new \Exception('MIME type no permitido');
        }

        return true;
    }

    public function guardarArchivo(Denuncia $denuncia, UploadedFile $file, $usuario_id = null)
    {
        return DB::transaction(function () use ($denuncia, $file, $usuario_id) {
            // Validar
            $this->validarArchivo($file);

            //Generar nombre único
            $hash = hash('sha256', file_get_contents($file));
            $extension = strtolower($file->getClientOriginalExtension());
            $nombreArchivo = "{$denuncia->folio}_" . Str::random(10) . ".{$extension}";

            //Guardar en storage
            $ruta = Storage::disk($this->diskName)->putFileAs(
                "denuncias/{$denuncias->id}",
                $file,
                $nombreArchivo
            );

            // Determinar tipo
            $tipo = $this->determinarTipo($extension);

            // Registrar en BD
            $archivo = DenunciaArchivo::create([
                'denuncia_id' => $denuncia->id,
                'nombre_archivo' => $nombreArchivo,
                'nombre_original' => $file->getClientOriginalName(),
                'tipo_archivo' => $tipo,
                'ruta_archivo' => $ruta,
                'tamaño_bytes' => $file->getSize(),
                'hash_archivo' => $hash,
                'fecha_carga' => now(),
                'usuario_id' => $usuario_id
            ]);

            return $archivo;
        });
    }

    public function obtenerArchivo(Denuncia $denuncia)
    {
        return $denuncia->archivos()
            ->orderBy('fecha_carga', 'desc')
            ->get();
    }

    public function descargarArchivo(DenunciaArchivo $archivo)
    {
        if (!Storage::disk($this->diskName)->dba_exists($archivo->ruta_archivo)) {
            throw new \Exception('Archivo no encontrado');
        }

        return Storage::disk($this->diskName)->download(
            $archivo->ruta_archivo,
            $archivo->nombre_original
        );
    }

    public function eliminarArchivo(DenunciaArchivo $archivo)
    {
        return DB::transaction(function () use ($archivo){
            Storage::disk($this->diskName)->delete
            ($archivo->ruta_archivo);
            $archivo->delete();
        });
    }

    private function determinarTipo($extension): string
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'imagen';
        } elseif ($extension === 'pdf') {
            return 'pdf';
        } else {
            return 'documento';
        }
    }
}