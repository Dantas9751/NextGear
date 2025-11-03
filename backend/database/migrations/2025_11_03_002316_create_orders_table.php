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
            
            $table->decimal('quantidade_total', 10, 2); 
            // Status do Pedido
            $table->enum('status', ['pendente', 'processando', 'enviado', 'entregue', 'cancelado'])
                  ->default('pendente');
                  
            $table->string('codigo_rastreamento')->nullable();
            
            $table->timestamps();
            $table->foreignId('address_id')
                  ->nullable() // Permite nulo
                  ->after('user_id') // Opcional: só para organizar
                  ->constrained('addresses') // Liga à tabela 'addresses'
                  ->onDelete('set null'); // Se o endereço for apagado, fica 'null' na encomenda
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
