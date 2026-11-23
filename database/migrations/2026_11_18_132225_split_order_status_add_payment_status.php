<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
        });
        // dawny status "paid" przenosimy do nowej kolumny payment_status
        DB::table('orders')->where('status', 'paid')->update(['payment_status'=>'paid']);
        // zamówienia "pending" i "paid" dostają status realizacji "new"
        DB::table('orders')->whereIn('status', ['pending','paid'])->update(['status'=>'new']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->where('payment_status', 'paid')->update(['status'=>'paid']);
        DB::table('orders')->where('status', 'new')->update(['status'=> 'pending']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
