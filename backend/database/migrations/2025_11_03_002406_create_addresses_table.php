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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('rua'); // Corrigido para 'rua'
            $table->string('cidade'); // Corrigido para 'cidade'
            $table->string('estado', 2); // Corrigido para 'estado'
            $table->string('cep', 9); // Corrigido para 'cep'
            
            $table->boolean('is_shipping')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
