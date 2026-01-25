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
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('family_code')->unique();
            $table->string('social_status'); // متزوج، أرملة، مطلق، يتيم
            $table->string('governorate');
            $table->text('full_address');

            // النزوح
            $table->boolean('is_displaced')->default(false);
            $table->enum('displacement_type', ['inside', 'outside'])->nullable();
            $table->foreignId('displacement_center_id')->nullable()->constrained('displacement_centers');
            $table->text('displacement_address')->nullable();

            // الدخل
            $table->decimal('income', 10, 2)->nullable();
            $table->string('income_range'); // <500, 500-1000..
            $table->foreignId('income_source_id')->constrained('income_sources');

            // ملاحظة: لم نضع breadwinner_id هنا لتجنب التعليق الدائري عند الإنشاء الأول
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
