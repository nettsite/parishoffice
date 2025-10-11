<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('households', function (Blueprint $table) {
            // Add normalized mobile field
            $table->string('mobile_normalized')->nullable()->after('mobile');
            
            // Add unique index for normalized mobile (nullable unique)
            $table->unique('mobile_normalized');
        });
        
        // Populate existing records with normalized mobile numbers
        DB::statement("
            UPDATE households 
            SET mobile_normalized = CASE 
                WHEN mobile IS NOT NULL AND mobile != '' 
                THEN REGEXP_REPLACE(mobile, '[^0-9+]', '')
                ELSE NULL 
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropUnique(['mobile_normalized']);
            $table->dropColumn('mobile_normalized');
        });
    }
};
