<?php

namespace App\Filament\Resources\GroupTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\CheckboxList;
use Spatie\Permission\Models\Permission;

class GroupTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(3),

                ColorPicker::make('color')
                    ->default('#6366f1'),

                Toggle::make('is_active')
                    ->default(true)
                    ->label('Active'),

                CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->options(function () {
                        return Permission::where('name', 'like', 'member.%')->pluck('name', 'name');
                    })
                    ->label('Group Type Permissions')
                    ->helperText('These permissions will be available to group leaders when managing members of this group type.')
                    ->columnSpanFull(),
            ]);
    }
}
