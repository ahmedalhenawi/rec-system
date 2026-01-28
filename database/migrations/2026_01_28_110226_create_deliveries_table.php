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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->string('national_id')->index();
            $table->string('beneficiary_name');
            $table->string('receipt_code');
            $table->timestamp('received_at');
            $table->timestamps();

            //  منع تكرار استلام نفس الطرد لنفس الهوية
            $table->unique(['package_id', 'national_id'], 'unique_delivery_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
