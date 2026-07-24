<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Cabecera
            'id_orden'    => 'required|integer|exists:ordenes,id',
            'id_oferta'   => 'required|integer|exists:ofertas,id',
            'id_cliente'  => 'required|integer|exists:clientes,id',
            'id_usuario'  => 'required|integer|exists:usuarios,id',
            'fecha_cad'   => 'required|date|date_format:Y-m-d',
            'monto_total' => 'required|numeric|min:0',
            'descuento'   => 'nullable|numeric|min:0',
            'estado'      => 'required|string|max:50',

            // 🔥 Detalles (opcionales)
            'detalles' => 'nullable|array',
            'detalles.*.id_equipo' => 'nullable|integer|exists:equipos,id_equipo',
            'detalles.*.id_repuesto' => 'nullable|integer|exists:repuestos,id_repuesto',
            'detalles.*.precio' => 'nullable|numeric|min:0',
            'detalles.*.cantidad' => 'nullable|integer|min:1',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'required'    => 'El campo :attribute es obligatorio.',
            'integer'     => 'El campo :attribute debe ser un número entero.',
            'numeric'     => 'El campo :attribute debe ser un número válido.',
            'exists'      => 'El :attribute seleccionado no es válido en el sistema.',
            'date_format' => 'La fecha debe cumplir el formato Año-Mes-Día (YYYY-MM-DD).',
            'detalles.*.id_equipo.exists' => 'El equipo seleccionado no existe.',
            'detalles.*.id_repuesto.exists' => 'El repuesto seleccionado no existe.',
        ];
    }
}