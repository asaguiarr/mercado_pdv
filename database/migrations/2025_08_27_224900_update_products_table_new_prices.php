<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // remover a coluna antiga
            $table->dropColumn('price');

            // adicionar as novas
            $table->decimal('cost_price', 10, 2)->after('name');
            $table->decimal('profit_margin', 5, 2)->after('cost_price');
            $table->decimal('sale_price', 10, 2)->after('profit_margin');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_price', 'profit_margin', 'sale_price']);
            $table->decimal('price', 10, 2);
        });
    }
};
