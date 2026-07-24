<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Http\Requests\ProveedorRequest;
use Exception;

class ProveedorController extends Controller
{
    public function index()
    {
        try {
            $proveedores = Proveedor::where('estado', 1)->get();

            return response()->json([
                'success' => true,
                'data' => $proveedores
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los proveedores.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ProveedorRequest $request)
    {
        try {
            $data = $request->validated();
            $data['estado'] = 1;

            $proveedor = Proveedor::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Proveedor creado correctamente.',
                'data' => $proveedor
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $proveedor = Proveedor::where('estado', 1)->find($id);

            if (!$proveedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado o inactivo.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $proveedor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ProveedorRequest $request, $id)
    {
        try {
            $proveedor = Proveedor::where('estado', 1)->find($id);

            if (!$proveedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado o inactivo.'
                ], 404);
            }

            $proveedor->update($request->validated());
            $proveedor->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado correctamente.',
                'data' => $proveedor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $proveedor = Proveedor::where('estado', 1)->find($id);

            if (!$proveedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado o ya inactivo.'
                ], 404);
            }

            $proveedor->update(['estado' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente (estado = 0).'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $proveedor = Proveedor::find($id);

            if (!$proveedor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado.'
                ], 404);
            }

            $proveedor->update(['estado' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Proveedor reactivado correctamente.',
                'data' => $proveedor
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}