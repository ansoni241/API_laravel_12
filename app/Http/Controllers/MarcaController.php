<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $marcas = Marca::all();
            $meta = [
                'total' => $marcas->count(),
                // 'page' => $marcas->currentPage(),
            ];
            return ApiResponse::success($marcas, 'Lista de Marcas', 200, $meta);
            // throw new Exception('Simulated error for testing');
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener las marcas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:marcas|string|max:255',
                // 'descripcion' => 'nullable|string|max:500',
            ]);

            $marca = Marca::create($request->all());
            return ApiResponse::success($marca, 'Marca creada exitosamente', 201);
        } catch (ValidationException $e) {
            return ApiResponse::error('Error de validación: ', 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $marca = Marca::findOrFail($id);
            return ApiResponse::success($marca, 'Marca encontrada', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no encontrada: ', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $request->validate([
                'nombre' => ['required', Rule::unique('marcas')->ignore($marca), 'string', 'max:255'],
            ]);

            $marca->update($request->all());

            return ApiResponse::success($marca, 'Marca actualizada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no encontrada: ', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error de validación: ' . $e->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $marca = Marca::findOrFail($id);
            $marca->delete();
            return ApiResponse::success('Marca eliminada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no encontrada: ', 404);
        }
    }
    public function productosPorMarca($id)
    {
        try {
            $marca = Marca::with('productos')->findOrFail($id);
            return ApiResponse::success($marca, 'Marca y lista de productos', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Marca no encontrada: ', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener los productos: ' . $e->getMessage(), 500);
        }
    }
}
