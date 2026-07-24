<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Http\Requests\CompraRequest;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleCompra;
use Exception;

class CompraController extends Controller
{
    public function index()
    {
        try {
            $compras = Compra::with(['proveedor', 'detalles'])
                ->where('estado', 1)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $compras
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las compras.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(CompraRequest $request)
    {
        try {
            $data = $request->validated();
            $data['estado'] = 1;

            $compra = DB::transaction(function () use ($data) {
                $compra = Compra::create($data);

                foreach ($data['detalles'] as $detalleData) {
                    $detalleData['id_compra'] = $compra->id_compra;
                    $detalleData['estado'] = 1;
                    DetalleCompra::create($detalleData);
                }

                return $compra;
            });

            $compra->load(['proveedor', 'detalles']);

            return response()->json([
                'success' => true,
                'message' => 'Compra y detalles registrados correctamente.',
                'data' => $compra
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra y detalles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $compra = Compra::with(['proveedor', 'detalles'])
                ->where('estado', 1)
                ->find($id);

            if (!$compra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o inactiva.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $compra
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar la compra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(CompraRequest $request, $id)
    {
        try {
            $compra = Compra::where('estado', 1)->find($id);

            if (!$compra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o inactiva.'
                ], 404);
            }

            $data = $request->validated();

            DB::transaction(function () use ($compra, $data) {
                $compra->update($data);

                DetalleCompra::where('id_compra', $compra->id_compra)->update(['estado' => 0]);

                foreach ($data['detalles'] as $detalleData) {
                    $detalleData['id_compra'] = $compra->id_compra;
                    $detalleData['estado'] = 1;
                    DetalleCompra::create($detalleData);
                }
            });

            $compra->refresh();
            $compra->load(['proveedor', 'detalles']);

            return response()->json([
                'success' => true,
                'message' => 'Compra y detalles actualizados correctamente.',
                'data' => $compra
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la compra y detalles.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $compra = Compra::where('estado', 1)->find($id);

            if (!$compra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada o ya inactiva.'
                ], 404);
            }

            DB::transaction(function () use ($compra) {
                $compra->update(['estado' => 0]);
                DetalleCompra::where('id_compra', $compra->id_compra)->update(['estado' => 0]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Compra y detalles eliminados correctamente (estado = 0).'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la compra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $compra = Compra::find($id);
            if (!$compra) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compra no encontrada.'
                ], 404);
            }

            DB::transaction(function () use ($compra) {
                $compra->update(['estado' => 1]);
                DetalleCompra::where('id_compra', $compra->id_compra)->update(['estado' => 1]);
            });

            $compra->load(['proveedor', 'detalles']);

            return response()->json([
                'success' => true,
                'message' => 'Compra y detalles reactivados correctamente.',
                'data' => $compra
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar la compra.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByProveedor($id_proveedor)
    {
        try {
            $compras = Compra::with(['proveedor', 'detalles'])
                ->where('estado', 1)
                ->where('id_proveedor', $id_proveedor)
                ->get();

            if ($compras->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron compras para este proveedor.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $compras
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las compras del proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}