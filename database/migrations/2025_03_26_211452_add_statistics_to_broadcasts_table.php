<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('broadcasts', function (Blueprint $table) {
        $table->integer('total_recipients')->default(0);
        $table->integer('sent_count')->default(0);
        $table->integer('failed_count')->default(0);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            //
        });
    }
};
