<?php

namespace App\Filament\Resources\Groups\Schemas;

use App\Models\GroupType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Select::make('group_type_id')
                            ->options(fn() => GroupType::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->label('Type'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),

                        Select::make('leaders')
                            ->relationship('leaders', 'name', fn($query) => $query->role(['Group Leader']))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->label('Group Leaders')
                            ->helperText('Select users who will lead this group')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
