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
        // Dedicated Subscriptions Table for Company Workspace Billing
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Account Owner
            $table->string('stripe_id')->nullable()->index();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four', 4)->nullable();
            $table->string('subscription_status')->default('trialing'); // trialing, active, past_due, canceled
            $table->string('subscription_plan')->default('pro'); // starter, pro, enterprise
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->integer('max_agent_seats')->default(10);
            $table->integer('max_channels')->default(10);
            $table->timestamps();
        });

        // Remove billing columns from users table to keep it clean
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'stripe_id')) {
                try {
                    $table->dropIndex(['stripe_id']);
                } catch (\Throwable $e) {
                    // Index might not exist or already dropped
                }
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
            }
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
