<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_device_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_type');
            $table->char('user_id', 36);
            $table->string('token')->unique();
            $table->enum('platform', ['android', 'ios']);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
        });
    }
};
