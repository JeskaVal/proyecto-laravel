<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDenunciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_denunciante' => 'required|in:anonimo,identificado',
            'nombre_denunciante' => 'required_if:tipo_denunciante,identificado|string|max:255',
            'correo_denunciante' => 'required_if:tipo_denunciante,identificado|email',
            'telefono_denunciante' => 'nullable|string|max:20',
            'tipo_identificacion' => 'required_if:tipo_denunciante,identificado|string',
            'numero_identificacion' => 'required_if:tipo_denunciante,identificado|string|max:50',
            'titulo_denuncia' => 'required|string|min:10|max:255',
            'descripcion_denuncia' => 'required|string|min:50|max:10000',
            'fecha_hechos' => 'nullable|date',
            'lugar_hechos' => 'nullable|string|max:500',
            'dependencia_implicada' => 'nullable|string|max:255',
            'prioridad' => 'nullable|in:baja,media,alta,urgente',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_denunciante.required' => 'Debes indicar si es denuncia anónima o identificada',
            'titulo_denuncia.min' => 'El título debe tener al menos 10 caracteres',
            'descripcion_denuncia.min' => 'La descripción debe tener al menos 50 caracteres',
            'correo_denunciante.email' => 'El correo electrónico no es válido',
        ];
    }
}