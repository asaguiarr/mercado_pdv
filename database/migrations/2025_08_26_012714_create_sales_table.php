<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabela de vendas
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuário que realizou a venda
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null'); // Cliente (opcional)
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['dinheiro', 'debito', 'credito', 'pix', 'misto']);
            $table->decimal('cash_received', 10, 2)->nullable(); // opcional
            $table->decimal('cash_change', 10, 2)->nullable();   // opcional
            $table->timestamps();
        });

        // Tabela de itens da venda
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade'); // referência para a venda
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // produto vendido
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // preço unitário no momento da venda
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
