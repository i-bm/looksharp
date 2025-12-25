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
        Schema::create('employer_company_testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employer_company_id')->constrained('employer_companies')->onDelete('cascade');
            $table->string('employee_name');
            $table->string('employee_title')->nullable();
            $table->text('testimonial');
            $table->string('photo_url')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employer_company_id', 'display_order'], 'emp_comp_testimonial_order_idx');
            $table->index(['employer_company_id', 'is_featured'], 'emp_comp_testimonial_featured_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_company_testimonials');
    }
};
