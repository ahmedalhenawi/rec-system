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
        Schema::table('families', function (Blueprint $table) {
            $table->foreignId('addition_source_id')->nullable()->constrained();
            $table->foreignId('addition_reason_id')->nullable()->constrained();

            $table->text('addition_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropForeign('addition_source_id');
            $table->dropForeign('addition_reason_id');

            $table->dropColumn('addition_notes');
        });
    }
};
