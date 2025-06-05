<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VersionCountriesRelationManager extends RelationManager
{
    protected static string $relationship = 'versionCountries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->columnSpanFull()
                    ->unique(
                        table: 'version_country',
                        column: 'country_id',
                        ignoreRecord: true,
                        modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule) {
                            return $rule->where('version_id', $this->getOwnerRecord()->id);
                        }
                    )
                    ->validationMessages([
                        'unique' => 'This country is already associated with this version.',
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Add Country to Version')
                    ->modalDescription('Select a country to associate with this version')
                    ->modalWidth('xl'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('xl'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
