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
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->string('subscription_status')->default('trialing'); // trialing, active, past_due, canceled
            $table->string('subscription_plan')->default('pro'); // starter, pro, enterprise
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->integer('max_agent_seats')->default(10);
            $table->integer('max_channels')->default(10);
        });

        // Billing Invoices Audit Table
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('plan_name');
            $table->integer('amount_cents');
            $table->string('currency', 3)->default('usd');
            $table->string('status')->default('paid'); // paid, failed, pending
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_invoices');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_id',
                'pm_type',
                'pm_last_four',
                'subscription_status',
                'subscription_plan',
                'trial_ends_at',
                'subscription_ends_at',
                'max_agent_seats',
                'max_channels',
            ]);
        });
    }
};
