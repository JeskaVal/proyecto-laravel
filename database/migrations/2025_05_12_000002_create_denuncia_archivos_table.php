<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncia_archivos', function (Blueprint $table)
        {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->string('nombre_archivo');
            $table->string('nombre_original');
            $table->enum('tipo_archivo', ['imagen', 'pdf', 'documento', 'otro']);
            $table->string('ruta_archivo');
            $table->bigInteger('tamaño_bytes');
            $table->string('hash_archivo')->unique(); //evita duplicados
            $table->dateTime('fecha_carga');
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['denuncia_id', 'fecha_carga']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncia_archivos');
    }
};