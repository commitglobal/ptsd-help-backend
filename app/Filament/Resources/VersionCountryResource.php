<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\VersionResource\Pages;
use App\Models\Country;
use App\Models\Version;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VersionCountryResource\RelationManagers\VersionCountryLanguagesRelationManager;


use App\Models\VersionCountry;
use Filament\Forms;

class VersionCountryResource extends Resource
{
    protected static ?string $model = VersionCountry::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->sortable(),

                TextColumn::make('languages')
                    ->label(__('country.field.languages'))
                    ->badge()
                    ->getStateUsing(
                        fn(VersionCountry $record) =>
                        $record->countryLanguages
                            ->pluck('language.name')
                            ->filter()
                            ->join(', ')
                    ),

            ])
            ->defaultGroup(
                Tables\Grouping\Group::make('version.name')
                    ->collapsible(),
            )
            ->filters([
                SelectFilter::make('version')
                    ->relationship('version', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Version'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ])
            ->modifyQueryUsing(fn($query) => $query->with('countryLanguages.language'));
    }

    public static function getRelations(): array
    {
        return [
            VersionCountryLanguagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersionCountries::route('/'),
            'edit' => Pages\EditVersionCountry::route('/{record}/edit'),
        ];
    }
}