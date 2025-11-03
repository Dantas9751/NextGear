<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <-- IMPORTANTE: Adicionar isto

class ProductController extends Controller
{
    /**
     * Display a listing of the resource. (Público)
     */
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage. (Admin)
     */
    public function store(Request $request)
    {
        // 1. Verificação de Admin
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }
        
        // 2. Validação (agora inclui a imagem)
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048' // Valida o upload
        ]);

        $imageUrl = null;

        // 3. Lógica de Upload
        if ($request->hasFile('imagem')) {
            // Guarda a imagem na pasta 'public/produtos'
            // O Laravel 'sabe' que 'public' significa 'storage/app/public'
            $path = $request->file('imagem')->store('produtos', 'public');
            
            // Obtém o URL público (graças ao 'php artisan storage:link')
            $imageUrl = Storage::url($path);
        }

        // 4. Adiciona o URL ao array de dados
        $validated['imagem_url'] = $imageUrl;
        unset($validated['imagem']); // Remove o 'ficheiro' do array antes de guardar no DB

        // 5. Cria o produto
        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource. (Público)
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage. (Admin)
     */
    public function update(Request $request, Product $product)
    {
        // 1. Verificação de Admin
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // 2. Validação
        $validated = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|numeric|min:0',
            'estoque' => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        // 3. Lógica de Upload (se uma nova imagem for enviada)
        if ($request->hasFile('imagem')) {
            
            // Opcional, mas recomendado: Apagar a imagem antiga
            if ($product->imagem_url) {
                // Converte o URL (ex: /storage/produtos/foto.jpg) 
                // para o caminho do disco (ex: produtos/foto.jpg)
                $oldPath = str_replace(Storage::url(''), '', $product->imagem_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Guarda a nova imagem
            $path = $request->file('imagem')->store('produtos', 'public');
            $validated['imagem_url'] = Storage::url($path);
        }
        
        unset($validated['imagem']); // Remove o 'ficheiro'

        // 4. Atualiza o produto
        $product->update($validated);
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage. (Admin)
     */
    public function destroy(Product $product)
    {
        // 1. Verificação de Admin
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        // 2. Apagar a imagem (se existir)
        if ($product->imagem_url) {
            $path = str_replace(Storage::url(''), '', $product->imagem_url);
            Storage::disk('public')->delete($path);
        }

        // 3. Apagar o produto do DB
        $product->delete();
        return response()->json(null, 204);
    }
}