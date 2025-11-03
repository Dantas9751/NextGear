<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Chave Estrangeira para o Cliente
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->decimal('quantidade_total', 10, 2); // Corrigido para 'quantidade_total'
            
            // Status do Pedido
            $table->enum('status', ['pendente', 'processando', 'enviado', 'entregue', 'cancelado'])
                  ->default('pendente');
                  
            $table->string('codigo_rastreamento')->nullable(); // Corrigido para 'codigo_rastreamento'
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
