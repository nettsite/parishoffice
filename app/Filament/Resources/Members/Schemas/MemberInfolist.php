<?php

namespace App\Filament\Resources\Members\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MemberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextEntry::make('first_name'),
                        TextEntry::make('last_name'),
                        TextEntry::make('household.name')
                            ->label('Household'),
                        TextEntry::make('email')
                            ->label('Email address'),
                        TextEntry::make('phone'),
                        TextEntry::make('mobile'),
                        TextEntry::make('occupation'),
                        TextEntry::make('skills'),
                    ])
                    ->columns(2),

                Section::make('Baptism')
                    ->schema([
                        IconEntry::make('baptised')
                            ->boolean(),
                        TextEntry::make('baptism_date')
                            ->date(),
                        TextEntry::make('baptism_parish'),
                        TextEntry::make('baptism_certificate')
                            ->label('Baptism Certificate')
                            ->getStateUsing(function ($record) {
                                $media = $record->getFirstMedia('baptism_certificates');

                                return $media ? $media->name : 'No certificate uploaded';
                            })
                            ->url(function ($record) {
                                $media = $record->getFirstMedia('baptism_certificates');

                                return $media ? $media->getUrl() : null;
                            })
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Section::make('First Communion')
                    ->schema([
                        IconEntry::make('first_communion')
                            ->boolean(),
                        TextEntry::make('first_communion_date')
                            ->date(),
                        TextEntry::make('first_communion_parish'),
                        TextEntry::make('first_communion_certificate')
                            ->label('First Communion Certificate')
                            ->getStateUsing(function ($record) {
                                $media = $record->getFirstMedia('first_communion_certificates');

                                return $media ? $media->name : 'No certificate uploaded';
                            })
                            ->url(function ($record) {
                                $media = $record->getFirstMedia('first_communion_certificates');

                                return $media ? $media->getUrl() : null;
                            })
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Section::make('Confirmation')
                    ->schema([
                        IconEntry::make('confirmed')
                            ->boolean(),
                        TextEntry::make('confirmation_date')
                            ->date(),
                        TextEntry::make('confirmation_parish'),
                        TextEntry::make('confirmation_certificate')
                            ->label('Confirmation Certificate')
                            ->getStateUsing(function ($record) {
                                $media = $record->getFirstMedia('confirmation_certificates');

                                return $media ? $media->name : 'No certificate uploaded';
                            })
                            ->url(function ($record) {
                                $media = $record->getFirstMedia('confirmation_certificates');

                                return $media ? $media->getUrl() : null;
                            })
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),

                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
