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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('whatsapp_client_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'template', 'text', 'interactive'
            $table->string('phone_number');
            $table->string('template_name')->nullable();
            $table->string('language_code')->nullable();
            $table->json('parameters')->nullable();
            $table->json('interactive_content')->nullable();
            $table->text('text')->nullable();
            $table->string('status')->default('pending'); // pending, sent, failed, retrying
            $table->integer('attempts')->default(0);
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
