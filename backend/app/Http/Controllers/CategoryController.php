<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- ADICIONE ISTO

class CategoryController extends Controller
{
    /**
     * Função PÚBLICA (todos podem ver)
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Função PÚBLICA (todos podem ver)
     */
    public function show(Category $category)
    {
        $category->load('products'); 
        return response()->json($category);
    }

    /**
     * Função de ADMIN
     */
    public function store(Request $request)
    {
        // LÓGICA DE ADMIN (COMO O tccsigme)
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // Resto da sua função
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:categories',
        ]);
        $category = Category::create($validated);
        return response()->json($category, 201);
    }

    /**
     * Função de ADMIN
     */
    public function update(Request $request, Category $category)
    {
        // LÓGICA DE ADMIN
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // Resto da sua função
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:categories,nome,' . $category->id,
        ]);
        $category->update($validated);
        return response()->json($category);
    }

    /**
     * Função de ADMIN
     */
    public function destroy(Category $category)
    {
        // LÓGICA DE ADMIN
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // Resto da sua função
        $category->delete();
        return response()->json(null, 204);
    }
}