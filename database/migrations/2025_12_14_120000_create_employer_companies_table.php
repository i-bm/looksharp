<?php

use App\Enums\EmployerCompanyStatusEnum;
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
        Schema::create('employer_companies', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('created_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->string('legal_name');
            $table->string('trading_name')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();

            $table->string('website')->nullable();
            $table->string('linkedin_url')->nullable();

            $table->string('country')->default('Ghana');
            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->string('official_email')->nullable();
            $table->string('phone_number', 20)->nullable();

            $table->string('registration_number')->nullable();

            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_title')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('primary_contact_phone', 20)->nullable();

            $table->string('status')->default(EmployerCompanyStatusEnum::DRAFT->value);
            $table->timestamp('submitted_at')->nullable();

            $table->foreignUuid('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('suspended_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'submitted_at']);
            $table->index('legal_name');
            $table->index('official_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_companies');
    }
};

