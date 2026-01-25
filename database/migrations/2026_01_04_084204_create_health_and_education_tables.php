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
        // للأمراض المزمنة
        Schema::create('chronic_diseases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained("persons")->onDelete('cascade');
            $table->string('disease_name');
            $table->timestamps();
        });

        // للإعاقات
        Schema::create('disabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained("persons")->onDelete('cascade');
            $table->string('disability_type');
            $table->string('severity')->nullable();
            $table->timestamps();
        });

        // للتعليم والمبادرات
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained("persons")->onDelete('cascade');
            $table->string('education_level'); // المرحلة الدراسية
            $table->string('initiative_name')->nullable(); // المبادرة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chronic_diseases');
        Schema::dropIfExists('disabilities');
        Schema::dropIfExists('education');
    }
};
