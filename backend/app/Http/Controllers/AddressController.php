<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Lista todos os endereços do utilizador autenticado.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses; // Pega da relação
        return response()->json($addresses);
    }

    /**
     * Armazena um novo endereço para o utilizador autenticado.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:50',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|max:2', // Ex: 'SP'
            'cep' => 'required|string|max:10', // Ex: '12345-678'
            'complemento' => 'nullable|string|max:255',
        ]);

        // Associa o user_id automaticamente ao utilizador logado
        $address = Auth::user()->addresses()->create($validated);

        return response()->json($address, 201);
    }

    /**
     * Mostra um endereço específico (se pertencer ao user).
     */
    public function show(Address $address)
    {
        // Garante que o endereço pertence ao utilizador logado
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }
        
        return response()->json($address);
    }

    /**
     * Atualiza um endereço do utilizador.
     */
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'rua' => 'sometimes|string|max:255',
            'numero' => 'sometimes|string|max:50',
            'cidade' => 'sometimes|string|max:255',
            'estado' => 'sometimes|string|max:2',
            'cep' => 'sometimes|string|max:10',
            'complemento' => 'nullable|string|max:255',
        ]);

        $address->update($validated);

        return response()->json($address);
    }

    /**
     * Remove um endereço do utilizador.
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $address->delete();

        return response()->json(null, 204);
    }
}