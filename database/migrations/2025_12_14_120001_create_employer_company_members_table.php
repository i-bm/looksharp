<?php

use App\Enums\EmployerCompanyMemberRoleEnum;
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
        Schema::create('employer_company_members', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('employer_company_id')->constrained('employer_companies')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('role')->default(EmployerCompanyMemberRoleEnum::COMPANY_ADMIN->value);

            $table->timestamps();

            $table->unique(['employer_company_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employer_company_members');
    }
};

