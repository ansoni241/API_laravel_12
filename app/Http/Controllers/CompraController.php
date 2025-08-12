<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Compra;
use App\Models\Producto;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $compras = Compra::with('productos')->get();
            $meta = [
                'total' => $compras->count(),
                // 'page' => $compras->currentPage(),
            ];
            return ApiResponse::success($compras, 'Lista de Compras', 200, $meta);
        } catch (Exception $e) {
            return ApiResponse::error('Error al obtener las compras: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $productos = $request->input('productos');
            //validar los productos
            if (empty($productos)) {
                return ApiResponse::error('No se han proporcionado productos para la compra.', 422);
            }
            //validar la lista de productos
            $validator = Validator::make($request->all(), [
                'productos' => 'required|array',
                'productos.*.producto_id' => 'required|integer|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
            ]);
            if ($validator->fails()) {
                return ApiResponse::error('Error de validación', 422, $validator->errors());
            }
            //validar productos duplicados
            $productoIds = array_column($productos, 'producto_id');
            if (count($productoIds) !== count(array_unique($productoIds))) {
                return ApiResponse::error('No se pueden agregar productos duplicados a la compra.', 422);
            }

            $totalPagar = 0;
            $subtotal = 0;
            $compraItems = [];

            //Iteracion de los productos para calcular el total a pagar
            foreach ($productos as $producto) {
                $productoB = Producto::find($producto['producto_id']);
                if (!$productoB) {
                    return ApiResponse::error('Producto no encontrado: ' . $producto['producto_id'], 404);
                }

                //validar la cantidad disponible de los productos
                if ($productoB->cantidad_disponible < $producto['cantidad']) {
                    return ApiResponse::error('Cantidad insuficiente para el producto: ' . $productoB->nombre, 422);
                }

                //Actualizarcion de la cantidad disponible de cada producto
                $productoB->cantidad_disponible -= $producto['cantidad'];
                $productoB->save();

                //Calculo de los importes
                $subtotal = $productoB->precio * $producto['cantidad'];
                $totalPagar += $subtotal;

                //Items de la compra
                $compraItems[] = [
                    'producto_id' => $productoB->id,
                    'precio' => $productoB->precio,
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $subtotal,
                ];
            }

            //Registro de la compra
            $compra = Compra::create([
                'subtotal' => $totalPagar,
                'total' => $totalPagar,
            ]);

            //Asociar los productos a la compra con sus cantidades y subtotales
            $compra->productos()->attach($compraItems);
            return ApiResponse::success($compra, 'Compra registrada exitosamente', 201);
        } catch (QueryException $e) {
            //error de consulta a la base de datos
            return ApiResponse::error('Error de base de datos: ', 500);
        } catch (Exception $e) {
            //error general
            return ApiResponse::error('Error inesperado ', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Compra $compra)
    {
        try {
            $compra->load('productos');
            return ApiResponse::success($compra, 'Compra encontrada', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Compra no encontrada: ', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Error inesperado: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compra $compra)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compra $compra)
    {
        //
    }
}
