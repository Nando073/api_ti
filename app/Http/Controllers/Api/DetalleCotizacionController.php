<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleCotizacion;
use Exception;

class DetalleCotizacionController extends Controller
{
    public function index()
    {
        try {
            $detalles = DetalleCotizacion::with(['cotizacion'])
                ->where('estado', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $detalles
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles de cotización.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $detalle = DetalleCotizacion::with(['cotizacion'])
                ->where('estado', 1)
                ->find($id);

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detalle de cotización no encontrado o inactivo.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $detalle
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el detalle de cotización.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByCotizacion($id_cotizacion)
    {
        try {
            $detalles = DetalleCotizacion::with(['cotizacion'])
                ->where('estado', 1)
                ->where('id_cotizacion', $id_cotizacion)
                ->get();

            if ($detalles->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron detalles activos para esta cotización.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $detalles
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles de la cotización.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}