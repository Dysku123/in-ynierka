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

        // Relacja (Klucz obcy)
        $table->foreignId('category_id')->constrained()->onDelete('cascade');

        // Podstawowe dane produktu
        $table->string('name');
        $table->string('slug')->unique();//optymalizacja SEO, unikalny identyfikator
        $table->text('description')->nullable();

        // Finanse i logistyka (Sklep e-commerce)
        $table->unsignedInteger('price'); // Cena w groszach
        $table->string('unit')->default('kg'); // Jednostka sprzedaży
        $table->decimal('weight_per_unit', 8, 3)->unsigned()->default(1.000); // Waga produktu na jednostkę
        $table->integer('stock_quantity')->default(0); // Stan magazynowy

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
