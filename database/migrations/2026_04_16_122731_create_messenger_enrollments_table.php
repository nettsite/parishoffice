<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_type');
            $table->char('user_id', 36);
            $table->string('status')->default('active');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            $table->unique(['user_type', 'user_id']);
            $table->index(['user_type', 'user_id']);
        });
    }
};
