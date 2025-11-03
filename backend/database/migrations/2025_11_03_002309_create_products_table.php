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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('descricao')->nullable(); 
            $table->string('imagem_url')->nullable();

            // Campos de Preço e Estoque
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('qunatidade_estoque')->default(0); 
            
            // Chave Estrangeira para Categoria
            $table->foreignId('category_id')
                  ->constrained() 
                  ->onDelete('restrict');
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
