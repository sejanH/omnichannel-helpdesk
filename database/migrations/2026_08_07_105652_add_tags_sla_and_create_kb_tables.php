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
        Schema::table('tickets', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('priority');
            $table->string('category')->nullable()->default('General')->after('tags');
            $table->timestamp('due_at')->nullable()->after('resolved_at');
        });

        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('General');
            $table->text('content');
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_articles');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['tags', 'category', 'due_at']);
        });
    }
};
