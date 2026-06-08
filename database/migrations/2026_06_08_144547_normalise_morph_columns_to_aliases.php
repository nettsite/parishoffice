<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Brings morph columns storing fully-qualified class names into line with the
     * short aliases registered in AppServiceProvider's Relation::morphMap(). Without
     * this, MorphMany/MorphToMany relations (e.g. Member::media(), Household::tokens())
     * query by the alias and silently return nothing for rows written before the morph
     * map existed — see docs/deployment-messenger-groups-migration.md for the original
     * (never-applied) plan for this migration.
     */
    public function up(): void
    {
        $tables = [
            'model_has_roles' => [
                'model_type' => [
                    'App\Models\User'   => 'user',
                    'App\Models\Member' => 'member',
                ],
            ],
            'model_has_permissions' => [
                'model_type' => [
                    'App\Models\User'   => 'user',
                    'App\Models\Member' => 'member',
                ],
            ],
            'media' => [
                'model_type' => [
                    'App\Models\Member' => 'member',
                    'App\Models\User'   => 'user',
                ],
            ],
            'personal_access_tokens' => [
                'tokenable_type' => [
                    'App\Models\User'      => 'user',
                    'App\Models\Member'    => 'member',
                    'App\Models\Household' => 'household',
                ],
            ],
        ];

        foreach ($tables as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $aliases) {
                foreach ($aliases as $fullClassName => $alias) {
                    DB::table($table)
                        ->where($column, $fullClassName)
                        ->update([$column => $alias]);
                }
            }
        }
    }
};
