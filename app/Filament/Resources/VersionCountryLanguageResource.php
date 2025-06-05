<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enum\VersionStatus;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\ContentForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Symptoms\SymptomsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\AmbientSoundsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\MindfulnessForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\MyFeelingsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\MyStrengthsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\PauseForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\RecreationalActivitiesForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\RelationshipsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\RidForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\SleepForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools\WorryTimeForm;
use App\Filament\Resources\VersionCountryLanguageResource\Pages;
use App\Models\VersionCountryLanguage;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\VersionCountry;
use App\Models\Language;

class VersionCountryLanguageResource extends Resource
{
    protected static ?string $model = VersionCountryLanguage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Select::make('version_country_id')
                            ->label('Version - Country')
                            ->required()
                            ->searchable()
                            ->options(function () {
                                return VersionCountry::with(['version', 'country'])
                                    ->get()
                                    ->mapWithKeys(function (VersionCountry $vc) {
                                        $label = "{$vc->version->name} - {$vc->country->name}";
                                        return [$vc->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->columnSpan(1),
                        Select::make('language_id')
                            ->relationship('language', 'name')
                            ->required()
                            ->unique(
                                table: 'version_country_language',
                                column: 'language_id',
                                ignoreRecord: true,
                                modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $livewire) {
                                    if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                        return $rule->where('version_country_id', $livewire->data['version_country_id']);
                                    }

                                    if ($livewire instanceof \Filament\Resources\Pages\EditRecord) {
                                        return $rule->where('version_country_id', $livewire->record->version_country_id);
                                    }

                                    return $rule;
                                }

                            )
                            ->validationMessages([
                                'unique' => 'This language is already associated with this country.',
                            ])
                    ])
                    ->columns(2),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('language.name')
                    ->label('Language')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([

            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultGroup(
                Tables\Grouping\Group::make('version_country_id') // Use the foreign key column for grouping
                    ->label('Version - Country')
                    ->getTitleFromRecordUsing(
                        fn(VersionCountryLanguage $record) =>
                        $record->versionCountry->version->name . ' - ' . $record->versionCountry->country->name
                    )
                    ->collapsible()
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersionCountryLanguages::route('/'),
            'create' => Pages\CreateVersionCountryLanguage::route('/create'),
            'view' => Pages\ViewVersionCountryLanguage::route('/{record}'),
            'edit' => Pages\EditVersionCountryLanguage::route('/{record}/edit'),
        ];
    }
}
