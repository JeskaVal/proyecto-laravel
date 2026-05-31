<?php

namespace Database\Seeders;

use App\Models\User;
use App\Mail\BienvenidaMail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
            'name'      =>  'Administrador Principal',
            'email'     =>  'admin@sistema.gob.mx',
            'password'  =>  'password_segura_123'
            ],
        ];

        foreach ($usuarios as $datos) {
            $user = User::firstOrCreate(
                ['email' => $datos['email']],
                [
                    'name'      => $datos['name'],
                    'password'  => Hash::make($datos['password']),
                ]
            );

            if ($user-> wasRecentlyCreated){
                Mail::to($user->email)->send(new BienvenidaMail($user));
            }
        }
    }
}