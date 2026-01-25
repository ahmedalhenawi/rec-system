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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('national_id')->unique();
            $table->date('dob'); // تاريخ الميلاد
            $table->enum('gender', ['male', 'female']);
            $table->string('relation'); // head, wife, son, daughter..
            $table->boolean('is_working')->default(false); // حالة العمل لرب الأسرة أو الأفراد

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
