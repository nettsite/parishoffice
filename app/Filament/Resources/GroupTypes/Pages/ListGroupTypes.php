<?php

namespace App\Filament\Resources\GroupTypes\Pages;

use App\Filament\Resources\GroupTypes\GroupTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGroupTypes extends ListRecords
{
    protected static string $resource = GroupTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
