<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->decimal('cost_price', 10, 2);     // preço de custo
            $table->decimal('profit_margin', 5, 2);   // margem de lucro (%)
            $table->decimal('sale_price', 10, 2);     // preço de venda
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
