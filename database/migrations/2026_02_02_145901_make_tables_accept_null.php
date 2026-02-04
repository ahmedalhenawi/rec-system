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
            // الحالة الاجتماعية والعنوان
            $table->string('social_status')->nullable()->change();
            $table->string('governorate')->nullable()->change();
            $table->text('full_address')->nullable()->change();

            // النزوح (تحويل الحقول المتبقية لـ null)
            $table->boolean('is_displaced')->nullable()->default(false)->change();
            $table->enum('displacement_type', ['inside', 'outside'])->nullable()->change();
            $table->unsignedBigInteger('displacement_center_id')->nullable()->change();
            $table->text('displacement_address')->nullable()->change();

            // الدخل
            $table->string('income_range')->nullable()->change();
            $table->unsignedBigInteger('income_source_id')->nullable()->change();
        });

        Schema::table('persons', function (Blueprint $table) {
            // للتأكيد على جدول الأفراد أيضاً
            $table->string('national_id')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->enum('gender', ['male', 'female'])->nullable()->change();
            $table->string('relation')->nullable()->change();
            $table->boolean('is_working')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
