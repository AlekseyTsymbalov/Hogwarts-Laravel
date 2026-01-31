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
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('section_id')
                ->references('id')
                ->on('sections')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable(false)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('section_id')
                ->references('id')
                ->on('sections')
                ->nullOnDelete();
        });
    }
};
