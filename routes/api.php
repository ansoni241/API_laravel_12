<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('marcas', MarcaController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('compras', CompraController::class);

Route::get('categorias/{categoria}/productos', [CategoriaController::class, 'productosPorCategoria']);
Route::get('marcas/{marca}/productos', [MarcaController::class, 'productosPorMarca']);
