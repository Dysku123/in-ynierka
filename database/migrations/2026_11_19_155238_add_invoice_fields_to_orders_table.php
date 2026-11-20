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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('invoice_company')->nullable()->after('paczkomat_point');
            $table->string('invoice_nip')->nullable()->after('invoice_company');
            $table->string('invoice_street')->nullable()->after('invoice_nip');
            $table->string('invoice_house_number')->nullable()->after('invoice_street');
            $table->string('invoice_apartment_number')->nullable()->after('invoice_house_number');
            $table->string('invoice_postal_code')->nullable()->after('invoice_apartment_number');
            $table->string('invoice_city')->nullable()->after('invoice_postal_code');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_company',
                'invoice_nip',
                'invoice_street',
                'invoice_house_number',
                'invoice_apartment_number',
                'invoice_postal_code',
                'invoice_city',
            ]);
        });
    }
};
