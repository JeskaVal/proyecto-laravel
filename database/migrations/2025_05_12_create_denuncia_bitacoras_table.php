<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncia_bitacoras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->string('accion');
            $table->text('descripcion');
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('fecha_accion');
            $table->json('detalles_adicionales')->nullable();
            $table->timestamps();

            $table->index(['denuncia_id', 'fecha_accion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncia_bitacoras');
    }
};