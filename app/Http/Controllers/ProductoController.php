<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $productos = Producto::all();
            $meta = [
                'total' => $productos->count(),
                // 'page' => $productos->currentPage(),
            ];
            return ApiResponse::success($productos, 'Lista de Productos', 200, $meta);
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener los productos: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|unique:productos|string|max:255',
                'precio' => 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
            ]);

            $producto = Producto::create($request->all());
            return ApiResponse::success($producto, 'Producto creado exitosamente', 201);
        } catch (ValidationException $e) {
            $errors= $e->validator->errors()->toArray();
            if (isset($errors['categoria_id'])) {
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }
            if (isset($errors['marca_id'])) {
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);
            }
            return ApiResponse::error('Error de validación: ', 422, $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $producto = Producto::with(['categoria', 'marca'])
                ->findOrFail($id);
            return ApiResponse::success($producto, 'Producto encontrado', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado: ', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener el producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $request->validate([
                'nombre' => ['required', 'string', 'max:255', 'unique:productos,nombre,' . $producto->id],
                'precio' => 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
            ]);

            $producto->update($request->all());
            return ApiResponse::success($producto, 'Producto actualizado exitosamente', 200);
        } catch (ValidationException $e) {
            $errors= $e->validator->errors()->toArray();
            if (isset($errors['categoria_id'])) {
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }
            if (isset($errors['marca_id'])) {
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);
            }
            return ApiResponse::error('Error de validación: ', 422, $errors);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado: ', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al actualizar el producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->delete();
            return ApiResponse::success('Producto eliminado exitosamente', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Producto no encontrado: ', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error al eliminar el producto: ' . $e->getMessage(), 500);
        }
    }
}
