<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DenunciaBitacora extends Model
{
    use HasFactory;

    protected $table = 'denuncia_bitacoras';

    protected $fillable = [
        'denuncia_id',
        'accion',
        'descripcion',
        'estado_anterior',
        'estado_nuevo',
        'usuario_id',
        'fecha_accion',
        'detalles_adicionales'
    ];

    protected $casts = [
        'detalles_adicionales' => 'array',
        'fecha_accion' => 'datetime'
    ];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}