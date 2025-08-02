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
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->string('mobile')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->boolean('married')->default(false);
            $table->date('marriage_date')->nullable();
            $table->string('marriage_parish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('households');
    }
};
