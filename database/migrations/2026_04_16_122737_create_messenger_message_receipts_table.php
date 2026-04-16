<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_message_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->string('user_type');
            $table->char('user_id', 36);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messenger_messages')->cascadeOnDelete();
            $table->index(['user_type', 'user_id']);
            $table->unique(['message_id', 'user_type', 'user_id']);
        });
    }
};
