<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_message_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->enum('recipient_type', ['user', 'group', 'all']);
            $table->char('recipient_id', 36)->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messenger_messages')->cascadeOnDelete();
        });
    }
};
