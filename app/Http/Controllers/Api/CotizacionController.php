<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Http\Requests\CotizacionRequest;
use Illuminate\Support\Facades\DB;
use Exception;

class CotizacionController extends Controller
{
    public function index()
    {
        try {
            $cotizaciones = Cotizacion::with(['oferta', 'detalles'])
                ->where('estado', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cotizaciones
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cotizaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CotizacionRequest $request)
    {
        try {
            $data = $request->validated();
            $data['estado'] = 1;

            $cotizacion = DB::transaction(function () use ($data) {
                // 1. Crear la cotización
                $cotizacion = Cotizacion::create($data);

                // 2. Crear los detalles
                foreach ($data['detalles'] as $detalleData) {
                    $detalleData['id_cotizacion'] = $cotizacion->id_cotizacion;
                    $detalleData['estado'] = 1;
                    DetalleCotizacion::create($detalleData);
                }

                return $cotizacion;
            });

            $cotizacion->load(['oferta', 'detalles']);

            return response()->json([
                'success' => true,
                'message' => 'Cotización y detalles registrados correctamente.',
                'data' => $cotizacion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la cotización y detalles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cotizacion = Cotizacion::with(['oferta', 'detalles'])
                ->where('estado', 1)
                ->find($id);

            if (!$cotizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cotización no encontrada o inactiva.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cotizacion
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la cotización.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(CotizacionRequest $request, $id)
    {
        try {
            $cotizacion = Cotizacion::where('estado', 1)->find($id);

            if (!$cotizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cotización no encontrada o inactiva.'
                ], 404);
            }

            $data = $request->validated();

            DB::transaction(function () use ($cotizacion, $data) {
                // Actualizar cabecera
                $cotizacion->update($data);

                // Desactivar detalles antiguos
                DetalleCotizacion::where('id_cotizacion', $cotizacion->id_cotizacion)
                    ->update(['estado' => 0]);

                // Crear nuevos detalles
                foreach ($data['detalles'] as $detalleData) {
                    $detalleData['id_cotizacion'] = $cotizacion->id_cotizacion;
                    $detalleData['estado'] = 1;
                    DetalleCotizacion::create($detalleData);
                }
            });

            $cotizacion->refresh();
            $cotizacion->load(['oferta', 'detalles']);

            return response()->json([
                'success' => true,
                'message' => 'Cotización y detalles actualizados correctamente.',
                'data' => $cotizacion
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la cotización y detalles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cotizacion = Cotizacion::where('estado', 1)->find($id);

            if (!$cotizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cotización no encontrada o ya inactiva.'
                ], 404);
            }

            DB::transaction(function () use ($cotizacion) {
                // Desactivar cotización
                $cotizacion->update(['estado' => 0]);
                // Desactivar detalles
                DetalleCotizacion::where('id_cotizacion', $cotizacion->id_cotizacion)
                    ->update(['estado' => 0]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Cotización y detalles eliminados correctamente (estado = 0).'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la cotización.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $cotizacion = Cotizacion::find($id);

            if (!$cotizacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cotización no encontrada.'
                ], 404);
            }

            DB::transaction(function () use ($cotizacion) {
                $cotizacion->update(['estado' => 1]);
                DetalleCotizacion::where('id_cotizacion', $cotizacion->id_cotizacion)
                    ->update(['estado' => 1]);
            });

            $cotizacion->load(['oferta', 'detalles']);

            return response()->json([
                'success' => true,
                'message' => 'Cotización y detalles reactivados correctamente.',
                'data' => $cotizacion
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar la cotización.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByOferta($id_oferta)
    {
        try {
            $cotizaciones = Cotizacion::with(['oferta', 'detalles'])
                ->where('estado', 1)
                ->where('id_oferta', $id_oferta)
                ->get();

            if ($cotizaciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron cotizaciones activas para esta oferta.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cotizaciones
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cotizaciones de la oferta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByCliente($id_cliente)
    {
        try {
            $cotizaciones = Cotizacion::with(['oferta', 'detalles'])
                ->where('estado', 1)
                ->where('id_cliente', $id_cliente)
                ->get();

            if ($cotizaciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron cotizaciones activas para este cliente.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cotizaciones
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cotizaciones del cliente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}