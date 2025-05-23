<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enum\VersionStatus;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\Support\SupportForm;
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
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VersionCountryLanguageResource extends Resource
{
    protected static ?string $model = VersionCountryLanguage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema(
            [
                Card::make()->schema([
                    Tabs::make('Form Tabs')
                        ->tabs([
                            Tabs\Tab::make('Basic Info')
                                ->schema([
                                    Card::make()->schema([
                                        Select::make('version_id')
                                            ->relationship('version', 'name')
                                            ->required(),

                                        Select::make('country_id')
                                            ->relationship('country', 'name')
                                            ->required(),

                                        Select::make('language_id')
                                            ->relationship('language', 'name')
                                            ->required(),
                                    ]),
                                ]),

                            Tabs\Tab::make('Manage > Symptoms')
                                ->schema([
                                    Card::make()->schema(SymptomsForm::getSchema()),
                                ]),

                            Tabs\Tab::make('Manage > Tools')
                                ->schema([
                                    Card::make()->schema([
                                        Section::make('Relationships')
                                            ->schema(RelationshipsForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),
                                        Section::make('Ambient sounds')
                                            ->schema(AmbientSoundsForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('Mindfulness')
                                            ->schema(MindfulnessForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('Pause')
                                            ->schema(PauseForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('My feelings')
                                            ->schema(MyFeelingsForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('Worry time')
                                            ->schema(WorryTimeForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('Rid')
                                            ->schema(RidForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('Recreational activities')
                                            ->schema(RecreationalActivitiesForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('Sleep')
                                            ->schema(SleepForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                        Section::make('My strengths')
                                            ->schema(MyStrengthsForm::getSchema())
                                            ->collapsible()
                                            ->collapsed()
                                            ->compact(),

                                    ]),
                                ]),

                            Tabs\Tab::make('Support screen')
                                ->schema([
                                    Card::make()->schema(SupportForm::getSchema()),
                                ]),

                            Tabs\Tab::make('Learn screen')
                                ->schema([
                                    Card::make()->schema([

                                    ]),
                                ]),

                            Tabs\Tab::make('Track screen')
                                ->schema([
                                    Card::make()->schema([

                                    ]),
                                ]),
                        ]),
                ]),
            ]
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('version.name')
                    ->label('Version')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('language.name')
                    ->label('Language')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('version.status')
                    ->label('Status')
                    ->options(VersionStatus::options())
                    ->query(function (Builder $query, $state) {
                        if (blank($state['value'])) {
                            return $query; // No filtering when nothing is selected
                        }

                        return $query->whereHas('version', function (Builder $subQuery) use ($state) {
                            $subQuery->where('status', $state);
                        });
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
            ]);
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['version', 'country', 'language']); // Make sure these are eager loaded
    }
}
