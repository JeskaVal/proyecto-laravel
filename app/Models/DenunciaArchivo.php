<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaArchivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'denuncia_id',
        'nombre_archivo',
        'nombre_original',
        'tipo_archivo',
        'ruta_archivo',
        'tamaño_bytes',
        'hash_archivo',
        'fecha_carga',
        'usuario_id'
    ];

    protected $dates = ['fecha_carga'];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    //obtener URL descargable del archivo
    public function obtenerUrlDescarga()
    {
        return route('denuncias.descargar-archivo', ['archivo' => $this->id]);
    }
}