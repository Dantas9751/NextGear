<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário.
     */
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', 
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'is_admin' => false, 
        ]);

        $token = $user->createToken('ecommerce-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Autentica e loga o usuário.
     */
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($fields)) {
            return response()->json([
                'message' => 'Credenciais Inválidas.'
            ], 401);
        }

        $user = Auth::user();

        // Cria o token. Uma abordagem comum é deletar os tokens antigos, mas aqui criaremos um novo para esta sessão.
        // Se você quiser revogar o token anterior para forçar um único login ativo: $user->tokens()->delete();
        $token = $user->createToken('ecommerce-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Desloga o usuário (revoga o token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Deslogado com sucesso'
        ], 200);
    }
}
