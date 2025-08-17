<?php

namespace App\Filament\Resources\Members\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                SelectFilter::make('baptised')
                    ->label('Baptism Status')
                    ->options([
                        'all' => 'All Members',
                        'baptised' => 'Baptised',
                        'not_baptised' => 'Not Baptised',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? 'all') {
                            'baptised' => $query->where('baptised', true),
                            'not_baptised' => $query->where('baptised', false),
                            default => $query,
                        };
                    }),
                SelectFilter::make('first_communion')
                    ->label('First Communion Status')
                    ->options([
                        'all' => 'All Members',
                        'first_communion' => 'First Communion Received',
                        'not_first_communion' => 'First Communion Not Received',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? 'all') {
                            'first_communion' => $query->where('first_communion', true),
                            'not_first_communion' => $query->where('first_communion', false),
                            default => $query,
                        };
                    }),
                SelectFilter::make('confirmed')
                    ->label('Confirmation Status')
                    ->options([
                        'all' => 'All Members',
                        'confirmed' => 'Confirmed',
                        'not_confirmed' => 'Not Confirmed',
                    ])
                    ->default('all')
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? 'all') {
                            'confirmed' => $query->where('confirmed', true),
                            'not_confirmed' => $query->where('confirmed', false),
                            default => $query,
                        };
                    }),
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
