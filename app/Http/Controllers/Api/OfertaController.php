<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Oferta;
use App\Http\Requests\OfertaRequest;
use Exception;

class OfertaController extends Controller
{
    public function index()
    {
        try {
            $ofertas = Oferta::where('estado', 1)->get();

            return response()->json([
                'success' => true,
                'data' => $ofertas
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las ofertas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(OfertaRequest $request)
    {
        try {
            $data = $request->validated();
            $data['estado'] = 1;

            $oferta = Oferta::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Oferta creada correctamente.',
                'data' => $oferta
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la oferta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $oferta = Oferta::where('estado', 1)->find($id);

            if (!$oferta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oferta no encontrada o inactiva.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $oferta
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la oferta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(OfertaRequest $request, $id)
    {
        try {
            $oferta = Oferta::where('estado', 1)->find($id);

            if (!$oferta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oferta no encontrada o inactiva.'
                ], 404);
            }

            $oferta->update($request->validated());
            $oferta->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Oferta actualizada correctamente.',
                'data' => $oferta
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la oferta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $oferta = Oferta::where('estado', 1)->find($id);

            if (!$oferta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oferta no encontrada o ya inactiva.'
                ], 404);
            }

            $oferta->update(['estado' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Oferta eliminada correctamente (estado = 0).'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la oferta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $oferta = Oferta::find($id);

            if (!$oferta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Oferta no encontrada.'
                ], 404);
            }

            $oferta->update(['estado' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Oferta reactivada correctamente.',
                'data' => $oferta
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar la oferta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}