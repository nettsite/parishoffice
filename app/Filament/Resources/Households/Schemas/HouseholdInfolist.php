<?php

namespace App\Filament\Resources\Households\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HouseholdInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('address'),
                TextEntry::make('city'),
                TextEntry::make('province'),
                TextEntry::make('postal_code'),
                TextEntry::make('phone'),
                TextEntry::make('mobile'),
                TextEntry::make('email')
                    ->label('Email address'),
                IconEntry::make('married')
                    ->boolean(),
                TextEntry::make('marriage_date')
                    ->date(),
                TextEntry::make('marriage_parish'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
