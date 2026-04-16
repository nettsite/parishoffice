<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messenger_group_users', function (Blueprint $table) {
            $table->uuid('group_id');
            $table->string('user_type');
            $table->char('user_id', 36);

            $table->primary(['group_id', 'user_type', 'user_id']);
            $table->index(['user_type', 'user_id']);
        });
    }
};
