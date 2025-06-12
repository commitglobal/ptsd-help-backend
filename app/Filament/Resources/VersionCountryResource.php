<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\Forms\ToolsForm;
use App\Filament\Resources\VersionCountryResource\Pages;
use App\Filament\Resources\VersionCountryResource\RelationManagers\VersionCountryLanguagesRelationManager;
use App\Models\VersionCountry;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VersionCountryResource extends Resource
{
    protected static ?string $model = VersionCountry::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('version_id')
                    ->label('Version')
                    ->relationship('version', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),

                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->unique(
                        table: 'version_country',
                        column: 'country_id',
                        ignoreRecord: true,
                        modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $livewire) {
                            if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                return $rule->where('country_id', $livewire->data['country_id']);
                            }

                            if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                return $rule->where('country_id', $livewire->record->country_id);
                            }

                            return $rule;
                        }
                    )
                    ->validationMessages([
                        'unique' => 'This Country is already associated with this version.',
                    ]),

                Card::make()
                    ->heading('Tools Settings') // Alternative to label with different styling
                    ->description('Configure the available tools for this version-country combination')
                    ->schema(ToolsForm::getToolsSchema()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')
                    ->sortable(),

                TextColumn::make('languages')
                    ->label(__('country.field.languages'))
                    ->badge()
                    ->getStateUsing(
                        fn(VersionCountry $record) => $record->countryLanguages
                            ->pluck('language.name')
                            ->filter()
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
            ->filtersLayout(FiltersLayout::AboveContent)

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'create' => Pages\CreateVersionCountry::route('/create'),
            'edit' => Pages\EditVersionCountry::route('/{record}/edit'),
            'view' => Pages\ViewVersionCountry::route('/{record}'),
        ];
    }
}
