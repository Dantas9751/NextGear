<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                        ->with('items.product')
                        ->latest()
                        ->get();
        
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id,user_id,' . Auth::id(),
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantidade' => 'required|integer|min:1', // Corrigido
        ]);

        $order = DB::transaction(function () use ($request, $validated) {
            $precoTotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                // Usamos lockForUpdate para evitar problemas de concorrência no estoque
                $product = Product::lockForUpdate()->find($item['product_id']);
                
                if ($product->estoque < $item['quantidade']) { // Corrigido
                     throw new \Exception('Stock insuficiente para o produto: ' . $product->name);
                }

                $precoItem = $product->preco * $item['quantidade']; // Corrigido
                $precoTotal += $precoItem;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantidade' => $item['quantidade'], // Corrigido
                    'preco' => $product->preco, // Corrigido (Preço no momento da compra)
                ];
                
                // Abater o estoque
                $product->decrement('estoque', $item['quantidade']); // Corrigido
            }

            // 1. Criar a Encomenda (Order)
            $order = Order::create([
                'user_id' => Auth::id(),
                'preco_total' => $precoTotal,
                'status' => 'pendente', // Corrigido (de acordo com a migration)
                'address_id' => $validated['address_id'], // Lembre-se de adicionar 'address_id' à migration 'orders'
            ]);

            // 2. Criar os Itens da Encomenda (OrderItems)
            $order->items()->createMany($itemsData);

            return $order;
        });

        $order->load('items.product');

        return response()->json($order, 201);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $order->load('items.product', 'user');
        return response()->json($order);
    }
}