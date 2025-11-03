<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'descricao', 
        'price', 
        'qunatidade_estoque', 
        'category_id',
        'imagem_url'
    ];

    // --- RELACIONAMENTOS ELOQUENT ---

    /**
     * O Produto pertence a uma Categoria.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * O Produto está em muitos Itens de Pedido (OrderItem).
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}