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
        Schema::create('employer_company_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('employer_company_id')->constrained('employer_companies')->onDelete('cascade');
            $table->string('photo_url');
            $table->string('caption')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employer_company_id', 'display_order'], 'emp_comp_photo_order_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_company_photos');
    }
};
