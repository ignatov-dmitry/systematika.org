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
        Schema::table('recordslesson', function (Blueprint $table) {
            $table->index('record_id_mk');
            $table->index('user_id_mk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recordslesson', function (Blueprint $table) {
            //
        });
    }
};
