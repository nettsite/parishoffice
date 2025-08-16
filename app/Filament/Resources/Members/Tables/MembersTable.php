<?php

namespace App\Filament\Resources\Members\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('household.name')
                    ->toggleable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->searchable(),
                IconColumn::make('baptised')
                    ->boolean(),
                TextColumn::make('baptism_date')
                    ->toggleable()
                    ->date()
                    ->sortable(),
                TextColumn::make('baptism_parish')
                    ->toggleable()
                    ->searchable(),
                IconColumn::make('first_communion')
                    ->boolean(),
                TextColumn::make('first_communion_date')
                    ->toggleable()
                    ->date()
                    ->sortable(),
                TextColumn::make('first_communion_parish')
                    ->toggleable()
                    ->searchable(),
                IconColumn::make('confirmed')
                    ->boolean(),
                TextColumn::make('confirmation_date')
                    ->toggleable()
                    ->date()
                    ->sortable(),
                TextColumn::make('confirmation_parish')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
