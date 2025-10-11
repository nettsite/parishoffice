<?php

namespace App\Filament\Resources\Households\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HouseholdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('city')
                    ->default(null),
                TextInput::make('province')
                    ->default(null),
                TextInput::make('postal_code')
                    ->default(null),
                TextInput::make('mobile')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                Toggle::make('married')
                    ->required(),
                DatePicker::make('marriage_date'),
                TextInput::make('marriage_parish')
                    ->default(null),
                DateTimePicker::make('terms_accepted')
                    ->label('Terms & Conditions Accepted')
                    ->readOnly()
                    ->displayFormat('Y-m-d H:i:s')
                    ->placeholder('Terms not yet accepted'),
            ]);
    }
}
