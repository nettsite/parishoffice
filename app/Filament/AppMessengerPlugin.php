<?php

namespace App\Filament;

use Filament\Panel;
use NettSite\Messenger\Filament\MessengerPlugin;
use NettSite\Messenger\Filament\Resources\MessageResource;

/**
 * Extends the vendor MessengerPlugin to skip registration of the package's GroupResource.
 * Matthew's own GroupResource (auto-discovered from app/Filament/Resources) handles group
 * management using App\Models\Group, which extends the messenger Group model.
 */
class AppMessengerPlugin extends MessengerPlugin
{
    public function register(Panel $panel): void
    {
        $panel->resources([
            MessageResource::class,
        ]);
    }
}
