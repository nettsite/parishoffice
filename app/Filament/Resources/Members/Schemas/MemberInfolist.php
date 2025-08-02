<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('first_name'),
                TextEntry::make('last_name'),
                TextEntry::make('household_id')
                    ->numeric(),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('phone'),
                TextEntry::make('mobile'),
                IconEntry::make('baptised')
                    ->boolean(),
                TextEntry::make('baptism_date')
                    ->date(),
                TextEntry::make('baptism_parish'),
                IconEntry::make('first_communion')
                    ->boolean(),
                TextEntry::make('first_communion_date')
                    ->date(),
                TextEntry::make('first_communion_parish'),
                IconEntry::make('confirmed')
                    ->boolean(),
                TextEntry::make('confirmation_date')
                    ->date(),
                TextEntry::make('confirmation_parish'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
