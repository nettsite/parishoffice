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
        $aliasMap = [
            'App\Models\User'      => 'user',
            'App\Models\Member'    => 'member',
            'App\Models\Household' => 'household',
        ];

        // model_has_roles / model_has_permissions have a composite primary key
        // (relation_id, model_id, model_type) — both an old-format and an alias-format
        // row can exist for the same (relation_id, model_id) pair (e.g. a role assigned
        // twice, once before and once after the morph map landed). A blind UPDATE would
        // collide with the existing alias row's primary key, so duplicates are dropped
        // and only genuinely stale rows are renamed.
        $this->normalisePivotMorphColumn('model_has_roles', 'role_id', $aliasMap);
        $this->normalisePivotMorphColumn('model_has_permissions', 'permission_id', $aliasMap);

        // media / personal_access_tokens have no unique constraint spanning their morph
        // columns, so a straight rename cannot collide.
        $this->normaliseColumn('media', 'model_type', [
            'App\Models\Member' => 'member',
            'App\Models\User'   => 'user',
        ]);

        $this->normaliseColumn('personal_access_tokens', 'tokenable_type', $aliasMap);
    }

    /**
     * @param array<string, string> $aliasMap fully-qualified class name => morph alias
     */
    private function normalisePivotMorphColumn(string $table, string $relationKey, array $aliasMap): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($aliasMap as $fullClassName => $alias) {
            $staleRows = DB::table($table)
                ->where('model_type', $fullClassName)
                ->get([$relationKey, 'model_id']);

            foreach ($staleRows as $row) {
                $matchesExistingAliasRow = DB::table($table)
                    ->where($relationKey, $row->$relationKey)
                    ->where('model_id', $row->model_id)
                    ->where('model_type', $alias)
                    ->exists();

                $staleRow = DB::table($table)
                    ->where($relationKey, $row->$relationKey)
                    ->where('model_id', $row->model_id)
                    ->where('model_type', $fullClassName);

                if ($matchesExistingAliasRow) {
                    $staleRow->delete();
                } else {
                    $staleRow->update(['model_type' => $alias]);
                }
            }
        }
    }

    /**
     * @param array<string, string> $aliasMap fully-qualified class name => morph alias
     */
    private function normaliseColumn(string $table, string $column, array $aliasMap): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($aliasMap as $fullClassName => $alias) {
            DB::table($table)->where($column, $fullClassName)->update([$column => $alias]);
        }
    }
};
