<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\Household;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                Select::make('household_id')
                    ->label('Household')
                    ->required()
                    ->relationship('household', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('address'),
                        TextInput::make('city'),
                        TextInput::make('province'),
                        TextInput::make('postal_code'),
                        TextInput::make('phone'),
                        TextInput::make('mobile'),
                        TextInput::make('email')
                            ->email(),
                    ])
                    ->nullable(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('mobile')
                    ->default(null),
                TextInput::make('password')
                    ->password()
                    ->default(null),
                TextInput::make('occupation')
                    ->label('Occupation')
                    ->default(null),
                TextInput::make('skills')
                    ->label('Skills')
                    ->default(null),
                Toggle::make('baptised')
                    ->required(),
                DatePicker::make('baptism_date'),
                TextInput::make('baptism_parish')
                    ->default(null),
                Toggle::make('first_communion')
                    ->required(),
                DatePicker::make('first_communion_date'),
                TextInput::make('first_communion_parish')
                    ->default(null),
                Toggle::make('confirmed')
                    ->required(),
                DatePicker::make('confirmation_date'),
                TextInput::make('confirmation_parish')
                    ->default(null),
            ]);
    }
}
