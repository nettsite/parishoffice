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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignId('household_id')->nullable()->constrained('households')->nullOnDelete();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('password')->nullable();
            $table->boolean('baptised')->default(false);
            $table->date('baptism_date')->nullable();
            $table->string('baptism_parish')->nullable();
            $table->boolean('first_communion')->default(false);
            $table->date('first_communion_date')->nullable();
            $table->string('first_communion_parish')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->date('confirmation_date')->nullable();
            $table->string('confirmation_parish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
