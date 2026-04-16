<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messenger_messages', function (Blueprint $table) {
            $table->timestamp('failed_at')->nullable()->after('sent_at');
        });
    }
};
