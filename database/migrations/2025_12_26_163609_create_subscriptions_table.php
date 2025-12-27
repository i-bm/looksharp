<?php

use App\Enums\SubscriptionTierEnum;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('employer_company_id')->constrained('employer_companies')->cascadeOnDelete();

            $table->string('tier')->default(SubscriptionTierEnum::FREE->value);
            $table->string('billing_cycle')->nullable();

            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency')->default('GHS');

            $table->string('status')->default('pending_payment'); // active, cancelled, expired, pending_payment

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('renews_at')->nullable();

            $table->boolean('auto_renew')->default(true);

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Payment tracking
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('payment_status')->nullable(); // pending, success, failed

            $table->timestamps();
            $table->softDeletes();

            $table->index('employer_company_id');
            $table->index('status');
            $table->index('tier');
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
