<?php

use App\Enums\EmployerCompanyVerificationStatusEnum;
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
        Schema::table('employer_companies', function (Blueprint $table) {
            $table->string('ghana_card_document_url')->nullable()->after('registration_number');
            $table->string('business_registration_document_url')->nullable()->after('ghana_card_document_url');
            $table->string('verification_status')->default(EmployerCompanyVerificationStatusEnum::PENDING->value)->after('business_registration_document_url');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->foreignUuid('verified_by_user_id')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();

            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employer_companies', function (Blueprint $table) {
            $table->dropIndex(['verification_status']);
            $table->dropForeign(['verified_by_user_id']);
            $table->dropColumn([
                'ghana_card_document_url',
                'business_registration_document_url',
                'verification_status',
                'verified_at',
                'verified_by_user_id',
            ]);
        });
    }
};
