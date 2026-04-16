<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables (empty — groups were never implemented)
        Schema::dropIfExists('group_member');
        Schema::dropIfExists('group_leaders');
        Schema::dropIfExists('groups');

        // Recreate group_member with UUID group_id referencing messenger_groups
        Schema::create('group_member', function (Blueprint $table) {
            $table->id();
            $table->uuid('group_id');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['group_id', 'member_id']);
            // Must use uuid column type — char(36) cannot FK to MariaDB native uuid PK
            $table->foreign('group_id')->references('id')->on('messenger_groups')->onDelete('cascade');
        });

        // Recreate group_leaders with UUID group_id referencing messenger_groups
        Schema::create('group_leaders', function (Blueprint $table) {
            $table->id();
            $table->uuid('group_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('appointed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['group_id', 'user_id']);
            $table->foreign('group_id')->references('id')->on('messenger_groups')->onDelete('cascade');
        });

        // Matthew-specific group metadata — kept separate so messenger tables stay untouched
        Schema::create('group_details', function (Blueprint $table) {
            $table->uuid('group_id')->primary();
            $table->text('description')->nullable();
            $table->foreignId('group_type_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('messenger_groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_details');
        Schema::dropIfExists('group_leaders');
        Schema::dropIfExists('group_member');

        // Restore original tables (empty stubs — no data to preserve)
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('group_type_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('group_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['group_id', 'member_id']);
        });

        Schema::create('group_leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('appointed_at')->useCurrent();
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });
    }
};
