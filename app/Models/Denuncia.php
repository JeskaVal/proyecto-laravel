<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Denuncia extends Model

{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folio',
        'tipo_denunciante',
        'nombre_denunciante',
        'correo_denunciante',
        'telefono_denunciante',
        'tipo_identificacion',
        'numero_identificacion',
        'titulo_denuncia',
        'descripcion_denuncia',
        'fecha_hechos',
        'lugar_hechos',
        'dependencia_implicada',
        'estado',
        'prioridad',
        'fecha_recibida',
        'fecha_ultimaActualizacion',
        'usuario_id',
        'ip_origen',
        'navegador',
        'contrasena_acceso',
    ];

    protected $casts = [
        'fecha_hechos' => 'datetime',
        'fecha_recibida' => 'datetime',
        'fecha_ultimaActualizacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'ip_origen',
        'navegador',
        'contrasena_acceso',
        'deleted_at',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function archivos()
    {
        return $this->hasMany(DenunciaArchivo::class);
    }

    public function bitacora()
    {
        return $this->hasMany(DenunciaBitacora::class);
    }

    // Generar un folio único para cada denuncia

    public static function generarFolio()
    {
        $año = date('Y');
        $consecutivo = self::whereYear('created_at', $año)->count() + 1;
        return strtoupper("DEN-$año-" . str_pad($consecutivo, 6, '0', STR_PAD_LEFT));
    }
}