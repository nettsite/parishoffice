<?php

namespace App\Filament\Resources\GroupTypes;

use App\Filament\Resources\GroupTypes\Pages\CreateGroupType;
use App\Filament\Resources\GroupTypes\Pages\EditGroupType;
use App\Filament\Resources\GroupTypes\Pages\ListGroupTypes;
use App\Filament\Resources\GroupTypes\Schemas\GroupTypeForm;
use App\Filament\Resources\GroupTypes\Tables\GroupTypesTable;
use App\Models\GroupType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GroupTypeResource extends Resource
{
    protected static ?string $model = GroupType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;


    public static function form(Schema $schema): Schema
    {
        return GroupTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GroupTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGroupTypes::route('/'),
            'create' => CreateGroupType::route('/create'),
            'edit' => EditGroupType::route('/{record}/edit'),
        ];
    }
}
