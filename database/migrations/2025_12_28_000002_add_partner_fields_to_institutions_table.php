<?php

use App\Enums\InstitutionPartnershipTierEnum;
use App\Enums\InstitutionTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->enum('type', InstitutionTypeEnum::class)->nullable()->after('name');
            $table->string('location')->nullable()->after('type');

            $table->boolean('is_partner')->default(false)->after('is_active');
            $table->enum('partnership_tier', InstitutionPartnershipTierEnum::class)->nullable()->after('is_partner');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['type', 'location', 'is_partner', 'partnership_tier']);
        });
    }
};

