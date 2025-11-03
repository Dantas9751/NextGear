<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rua',
        'cidade',
        'estado',
        'cep',
        'numero',
        'complemento',
        'is_shipping', // Campo opcional para indicar se é o endereço principal de envio
    ];

    /**
     * O Endereço pertence a um Usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}