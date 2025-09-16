<?php

namespace App\Filament\Resources\GroupTypes\Pages;

use App\Filament\Resources\GroupTypes\GroupTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGroupType extends CreateRecord
{
    protected static string $resource = GroupTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
