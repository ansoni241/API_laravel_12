<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categorias = Categoria::all();
            $meta = [
                'total' => $categorias->count(),
                // 'page' => $categorias->currentPage(),
            ];
            return ApiResponse::success($categorias, 'Lista de Categorías', 200, $meta);
            // throw new Exception('Simulated error for testing');
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener las categorías: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:categorias|string|max:255',
                // 'descripcion' => 'nullable|string|max:500',
            ]);

            $categoria = Categoria::create($request->all());
            return ApiResponse::success($categoria, 'Categoría creada exitosamente', 201);
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
            $categoria = Categoria::findOrFail($id);
            return ApiResponse::success($categoria, 'Categoría encontrada', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada: ', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            $request->validate([
                'nombre' => ['required', Rule::unique('categorias')->ignore($categoria), 'string', 'max:255'],
            ]);

            $categoria->update($request->all());

            return ApiResponse::success($categoria, 'Categoría actualizada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada: ', 404);
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
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();
            return ApiResponse::success('Categoría eliminada exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Categoría no encontrada: ', 404);
        }
    }
}
