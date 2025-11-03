<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Importar os Controllers
|--------------------------------------------------------------------------
*/
// O seu controller de autenticação
use App\Http\Controllers\AuthController; 

// Nossos controllers de API (assumindo que estão em app/Http/Controllers/Api)
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AddressController;


/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
|
| Rotas que não exigem autenticação.
| (Registo, Login, Ver Produtos, Ver Categorias)
|
*/

// Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Loja (Produtos e Categorias)
// Route::apiResource() cria 5 rotas:
// 1. GET /products (index) - Pública
// 2. GET /products/{id} (show) - Pública
// 3. POST /products (store) - Protegida DENTRO do controller
// 4. PUT/PATCH /products/{id} (update) - Protegida DENTRO do controller
// 5. DELETE /products/{id} (destroy) - Protegida DENTRO do controller
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);


/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (CLIENTE)
|--------------------------------------------------------------------------
|
| Rotas que exigem que o utilizador (seja 'cliente' ou 'admin')
| esteja autenticado.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // --- Autenticação ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Gestão do Utilizador ---
    Route::get('/user', function (Request $request) {
        // Retorna o utilizador logado e já carrega os seus endereços
        return $request->user()->load('addresses'); 
    });

    // CRUD de Endereços (o utilizador gere os seus próprios endereços)
    Route::apiResource('addresses', AddressController::class);

    // --- Encomendas ---
    // O utilizador pode:
    // 1. GET /orders (index) - Ver as SUAS encomendas
    // 2. GET /orders/{id} (show) - Ver UMA das suas encomendas
    // 3. POST /orders (store) - Criar uma nova encomenda
    Route::apiResource('orders', OrderController::class)->only([
        'index', 'show', 'store'
    ]);
});
