<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables;

class CountriesRelationManager extends RelationManager
{
    protected static string $relationship = 'countries';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')->label(__('country.field.id'))->searchable()->sortable(),
                TextColumn::make('name')->label(__('country.field.name'))->searchable()->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Attach Country')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn($query) => $query->orderBy('name')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                DetachAction::make()->label('Detach')
            ]);
    }
}
