<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VersionCountryLanguagesRelationManager extends RelationManager
{
    protected static string $relationship = 'countryLanguages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('language_id')
                    ->relationship('language', 'name')
                    ->required()
                    ->unique(
                        table: 'version_country_language',
                        column: 'language_id',
                        ignoreRecord: true,
                        modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule) {
                            return $rule->where('version_country_id', $this->getOwnerRecord()->id);
                        }
                    )
                    ->validationMessages([
                        'unique' => 'This country is already associated with this version.',
                    ]),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('language.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Add Language to Version Country')
                    ->modalDescription('Select a language to associate with this country version')
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
