<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'quantidade_total', 
        'status',
        'codigo_rastreamento'
    ];

    /**
     * O Pedido pertence a um Usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * O Pedido tem muitos Itens de Pedido (OrderItem).
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}