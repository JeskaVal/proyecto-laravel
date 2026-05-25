<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema:: create('denuncias', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->enum('tipo_denunciante', ['anonimo', 'identificado']);
            $table->string('nombre_denunciante')->nullable();
            $table->string('correo_denunciante')->nullable();
            $table->string('telefono_denunciante')->nullable();
            $table->string('numero_identificacion')->nullable();
            $table->string('titulo_denuncia');
            $table->longText('descripcion_denuncia');
            $table->date('fecha_hechos')->nullable();
            $table->string('lugar_hechos')->nullable();
            $table->string('dependencia_implicada')->nullable();
            $table->enum('estado', ['recibida', 'en_revision', 'en_proceso', 'resuelta', 'archivada'])->default('recibida');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->dateTime('fecha_recibida');
            $table->dateTime('fecha_ultimaActualizacion')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->ipAddress('ip_origen')->nullable();
            $table->string('navegador')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para optimizar consultas
            $table->index('folio');
            $table->index('estado');
            $table->index('fecha_recibida');
            $table->index('usuario_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};