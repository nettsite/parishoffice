<?php

namespace App\Providers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register stable morph aliases — prevents stored class names from breaking on namespace refactors
        // Non-enforcing morphMap() allows full class names already stored in live DB to coexist with aliases.
        // Switch back to enforceMorphMap() after running the morph normalisation migration.
        Relation::morphMap([
            'user'      => User::class,
            'member'    => Member::class,
            'household' => \App\Models\Household::class,
        ]);

        // Implicitly grant "Developer" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user) {
            return $user->hasRole('Developer') ? true : null;
        });
    }
}
