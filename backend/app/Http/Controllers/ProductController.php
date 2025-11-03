<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- ADICIONE ISTO

class ProductController extends Controller
{
    /**
     * Função PÚBLICA
     */
    public function index(Request $request)
    {
        // ... (lógica de listar produtos)
        $query = Product::query()->with('category');
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $products = $query->get();
        return response()->json($products);
    }

    /**
     * Função PÚBLICA
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Função de ADMIN
     */
    public function store(Request $request)
    {
        // LÓGICA DE ADMIN
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }
        
        // Resto da sua função
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Função de ADMIN
     */
    public function update(Request $request, Product $product)
    {
        // LÓGICA DE ADMIN
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // Resto da sua função
        $validated = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|numeric|min:0',
            'estoque' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
        ]);
        $product->update($validated);
        return response()->json($product);
    }

    /**
     * Função de ADMIN
     */
    public function destroy(Product $product)
    {
        // LÓGICA DE ADMIN
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // Resto da sua função
        $product->delete();
        return response()->json(null, 204);
    }
}