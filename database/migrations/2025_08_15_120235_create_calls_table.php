<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('caller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['initiated', 'ringing', 'accepted', 'declined', 'ended', 'missed'])->default('initiated');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['caller_id', 'receiver_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
