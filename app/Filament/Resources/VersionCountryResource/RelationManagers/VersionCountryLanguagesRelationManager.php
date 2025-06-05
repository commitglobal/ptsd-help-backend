<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\RelationManagers;

use App\Models\Country;
use App\Models\Version;
use App\Models\VersionCountry;
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
                Select::make('version_id')
                    ->label('Version')
                    ->options(Version::query()->pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->default(function ($livewire) {
                        return optional($livewire->getRecord()?->versionCountry)->version_id;
                    }),

                Select::make('country_id')
                    ->label('Country')
                    ->options(
                        fn (callable $get) => Country::whereHas('versionCountries', function ($query) use ($get) {
                            $query->where('version_id', $get('version_id'));
                        })->pluck('name', 'id')
                    )
                    ->required()
                    ->reactive(),

                Select::make('version_country_id')
                    ->label('Version - Country')
                    ->options(function () {
                        return VersionCountry::with(['version', 'country'])
                            ->get()
                            ->mapWithKeys(function (VersionCountry $vc) {
                                $label = "{$vc->version->name} - {$vc->country->name}";

                                return [$vc->id => $label];
                            })
                            ->toArray();
                    })
                    ->required()
                    ->hidden(), // Hide the raw field,
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
