<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'product_id', 
        'quantidade', 
        'preco'
    ];

    // --- RELACIONAMENTOS ELOQUENT ---

    /**
     * O Item do Pedido pertence a um Pedido.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * O Item do Pedido se refere a um Produto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}