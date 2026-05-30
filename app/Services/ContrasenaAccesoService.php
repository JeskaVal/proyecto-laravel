<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class ContrasenaAccesoService
{
    private array $palabras = [
        'Ajedrez', 'Virtud', 'Cosmos', 'Fuego', 'Bravo',
        'Tigre', 'Palma', 'Cedro', 'Bosque', 'Cumbre',
        'Sierra', 'Fuerza', 'Valor', 'Honor', 'Noble',
        'Pluma', 'Acero', 'Marte', 'Venus', 'Risco',
        'Templo', 'Puente', 'Cobre', 'Mango', 'Roble',
        'Sauce', 'Niebla', 'Trueno', 'Viento', 'Limon',
    ];

    private string $especiales = '!@#$%';
    private string $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private string $minusculas = 'abcdefghijklmnopqrstuvwxyz';
    private string $numeros = '0123456789';

    public function generarContrasena(): string
    {
        $palabra = $this->palabras[array_rand($this->palabras)];

        $extras = [
            $this->especiales[random_int(0, strlen($this->especiales) - 1)],
            $this->especiales[random_int(0, strlen($this->especiales) - 1)],
            $this->numeros[random_int(0, strlen($this->numeros) - 1)],
            $this->numeros[random_int(0, strlen($this->numeros) - 1)],
            $this->mayusculas[random_int(0, strlen($this->mayusculas) - 1)],
            $this->minusculas[random_int(0, strlen($this->minusculas) - 1)],
        ];

        shuffle($extras);

        $contrasena = $palabra . implode('', $extras);

        while (strlen($contrasena) < 12) {
            $contrasena .= $this->numeros[random_int(0, strlen($this->numeros) - 1)];
        }

        return substr($contrasena, 0, 16);
    }

    public function hashear(string $contrasena): string
    {
        return Hash::make($contrasena);
    }

    public function verificar(string $contrasena, string $hash): bool
    {
        return Hash::check($contrasena, $hash);
    }
}
