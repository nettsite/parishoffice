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
        Schema::table('members', function (Blueprint $table) {
            $table->boolean('married')->default(false)->after('confirmation_parish');
            $table->date('marriage_date')->nullable()->after('married');
            $table->string('marriage_parish')->nullable()->after('marriage_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['married', 'marriage_date', 'marriage_parish']);
        });
    }
};
