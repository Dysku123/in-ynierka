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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id', 'portion_weight'], 'cart_user_product_portion_unique');
            $table->unique(['session_id', 'product_id', 'portion_weight'], 'cart_session_product_portion_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('cart_user_product_portion_unique');
            $table->dropUnique('cart_session_product_portion_unique');
        });
    }
};
