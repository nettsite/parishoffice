<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('first_name')
                            ->required(),
                        TextInput::make('last_name')
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->maxDate(now())
                            ->default(null),
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

                        // TODO: maybe allow members to log in sometime
                        // TextInput::make('password')
                        //     ->password()
                        //     ->revealable()
                        //     ->default(null),
                        // TextInput::make('password_confirmation')
                        //     ->label('Confirm Password')
                        //     ->password()
                        //     ->revealable()
                        //     ->same('password')
                        //     ->default(null),

                        TextInput::make('occupation')
                            ->label('Occupation')
                            ->default(null),
                        TextInput::make('skills')
                            ->label('Skills')
                            ->default(null),
                    ])
                    ->columns(2),

                Section::make('Baptism')
                    ->schema([
                        Toggle::make('baptised')
                            ->required(),
                        DatePicker::make('baptism_date'),
                        TextInput::make('baptism_parish')
                            ->default(null),
                        SpatieMediaLibraryFileUpload::make('baptism_certificate')
                            ->label('Baptism Certificate')
                            ->collection('baptism_certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10240) // 10MB
                            ->openable()
                            ->previewable(false)
                            ->panelLayout('compact'),
                    ])
                    ->columns(2),

                Section::make('First Communion')
                    ->schema([
                        Toggle::make('first_communion')
                            ->required(),
                        DatePicker::make('first_communion_date'),
                        TextInput::make('first_communion_parish')
                            ->default(null),
                        SpatieMediaLibraryFileUpload::make('first_communion_certificate')
                            ->label('First Communion Certificate')
                            ->collection('first_communion_certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10240) // 10MB
                            ->openable()
                            ->previewable(false)
                            ->panelLayout('compact'),
                    ])
                    ->columns(2),

                Section::make('Confirmation')
                    ->schema([
                        Toggle::make('confirmed')
                            ->required(),
                        DatePicker::make('confirmation_date'),
                        TextInput::make('confirmation_parish')
                            ->default(null),
                        SpatieMediaLibraryFileUpload::make('confirmation_certificate')
                            ->label('Confirmation Certificate')
                            ->collection('confirmation_certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->maxSize(10240) // 10MB
                            ->openable()
                            ->previewable(false)
                            ->panelLayout('compact'),
                    ])
                    ->columns(2),
            ]);
    }
}
