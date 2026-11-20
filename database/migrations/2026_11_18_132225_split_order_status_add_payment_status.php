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
        //zminaa oplacone na platnosac oplacone
        DB::table('orders')->where('status', 'paid')->update(['payment_status'=>'paid']);
        DB::table('orders')->whereIn('status', ['pending','paid'])->update(['status'=>'new']);//zmienaimy status na new dla wszystkich zamowien ktore sa pending lub paid
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
