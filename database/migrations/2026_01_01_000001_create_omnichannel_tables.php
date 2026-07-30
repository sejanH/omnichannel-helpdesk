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
        // Channels Table
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // web_chat, whatsapp, email, facebook, telegram, sms
            $table->string('icon')->nullable();
            $table->json('configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Contacts Table
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->json('external_ids')->nullable(); // {"whatsapp": "+123", "telegram": "id123"}
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tickets Table
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('channel_id')->constrained('channels')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('first_responded_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // Messages Table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->enum('sender_type', ['customer', 'agent', 'system', 'bot']);
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_name');
            $table->text('content');
            $table->string('content_type')->default('text'); // text, image, file, system
            $table->json('attachments')->nullable();
            $table->boolean('is_internal_note')->default(false);
            $table->string('channel_message_id')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Canned Responses
        Schema::create('canned_responses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('shortcut')->unique();
            $table->text('content');
            $table->timestamps();
        });

        // SLA Policies
        Schema::create('sla_policies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->unique();
            $table->integer('first_response_target_minutes');
            $table->integer('resolution_target_minutes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_policies');
        Schema::dropIfExists('canned_responses');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('channels');
    }
};
