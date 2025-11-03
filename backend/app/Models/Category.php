<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug'];

    // --- RELACIONAMENTOS ELOQUENT ---

    /**
     * Uma Categoria tem muitos Produtos (Products).
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}