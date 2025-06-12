<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\Forms\Tools\AmbientSoundsForm;
use App\Filament\Resources\Forms\Tools\FeelingsForm;
use App\Filament\Resources\Forms\Tools\MindfulnessForm;
use App\Filament\Resources\Forms\Tools\MyFeelingsForm;
use App\Filament\Resources\Forms\Tools\MyStrengthsForm;
use App\Filament\Resources\Forms\Tools\PauseForm;
use App\Filament\Resources\Forms\Tools\RecreationalActivitiesForm;
use App\Filament\Resources\Forms\Tools\RelationshipsForm;
use App\Filament\Resources\Forms\Tools\RidForm;
use App\Filament\Resources\Forms\Tools\SleepForm;
use App\Filament\Resources\Forms\Tools\WorryTimeForm;
use App\Filament\Resources\Forms\ToolsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Forms\SymptomsForm;
use App\Filament\Resources\VersionCountryLanguageResource\Pages;
use App\Models\VersionCountry;
use App\Models\VersionCountryLanguage;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class VersionCountryLanguageResource extends Resource
{
    protected static ?string $model = VersionCountryLanguage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Display parent version and country information
                Placeholder::make('version_name')
                    ->label('Version')
                    ->content(function ($record) {
                        return $record?->versionCountry?->version?->name ?? '-';
                    })->visible(function ($record) {
                        return $record?->versionCountry != null;
                    }),

                Placeholder::make('country_name')
                    ->label('Country')
                    ->content(function ($record) {
                        return $record?->versionCountry?->country?->name ?? '-';
                    })->visible(function ($record) {
                        return $record?->versionCountry != null;
                    }),

                Select::make('version_country_id')
                    ->label('Version - Country')
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return VersionCountry::with(['version', 'country'])
                            ->get()
                            ->groupBy(fn($vc) => $vc->version->name)
                            ->sortKeys()
                            ->map(function ($group) {
                                return $group->mapWithKeys(function (VersionCountry $vc) {
                                    $label = "{$vc->version->name} / {$vc->country->name}";

                                    return [$vc->id => $label];
                                });
                            })
                            ->toArray();
                    })
                    ->reactive()
                    ->live()
                    ->visible(function ($record) {
                        return $record?->versionCountry == null;
                    }),

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
                    ]),

                Card::make()
                    ->heading(heading: 'Media management')
                    ->description('Configure the available media for the current version/country/language')
                    ->schema([

                        Tabs::make('Form Tabs')
                            ->tabs([

                                Tabs\Tab::make('Manage > Symptoms')
                                    ->schema(components: SymptomsForm::getMediaSchema()),

                                Tabs\Tab::make('Manage > Tools')
                                    ->schema(ToolsForm::getToolsMediaSchema()),

                                Tabs\Tab::make('Support screen')
                                    ->schema([
                                        Card::make()->schema([

                                        ]),
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

                Card::make()
                    ->heading(heading: 'Content management')
                    ->description('Configure the available content for the current version/country/language')
                    ->schema([

                        Tabs::make('Form Tabs')
                            ->tabs([

                                Tabs\Tab::make('Manage > Symptoms')
                                    ->schema(components: SymptomsForm::getContentSchema()),

                                Tabs\Tab::make('Manage > Tools')
                                    ->schema(ToolsForm::getContentSchema()),

                                Tabs\Tab::make('Manage > Tools > Ambient sounds')
                                    ->schema(AmbientSoundsForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['ambient-sounds'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['ambient-sounds'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Mindfulness')
                                    ->schema(MindfulnessForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['mindfulness']['enabled'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['mindfulness']['enabled'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Feelings ')
                                    ->schema(FeelingsForm::getContentSchema()),

                                Tabs\Tab::make('Manage > Tools > My feelings')
                                    ->schema(MyFeelingsForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['my-feelings'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['my-feelings'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > My strengths')
                                    ->schema(MyStrengthsForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['my-strengths'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['my-strengths'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Pause')
                                    ->schema(PauseForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['pause'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['pause'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Recreational Activities')
                                    ->schema(RecreationalActivitiesForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['recreational-activities']['enabled'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['recreational-activities']['enabled'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Relationships')
                                    ->schema(RelationshipsForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['relationships']['enabled'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['relationships']['enabled'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > RID')
                                    ->schema(RidForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['rid'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['rid'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Sleep')
                                    ->schema(SleepForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['sleep']['enabled'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['sleep']['enabled'] === true;
                                    }),

                                Tabs\Tab::make('Manage > Tools > Worry time')
                                    ->schema(WorryTimeForm::getContentSchema())
                                    ->visible(function ($get, $livewire) {
                                        // For existing records (edit)
                                        if ($record = $livewire->getRecord()) {
                                            return $record->versionCountry?->tools['worry-time'] === true;
                                        }

                                        // For new records (create)
                                        $versionCountryId = $get('version_country_id');
                                        if (!$versionCountryId) {
                                            return false;
                                        }

                                        $versionCountry = VersionCountry::find($versionCountryId);

                                        return $versionCountry?->tools['worry-time'] === true;
                                    }),

                                Tabs\Tab::make('Support screen')
                                    ->schema([
                                        Card::make()->schema([

                                        ]),
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
                Action::make('clone')
                    ->label('Clone')
                    ->icon('heroicon-o-document-duplicate')
                    ->modalHeading('Duplicate Language Configuration')
                    ->form([
                        \Filament\Forms\Components\Grid::make(2)
                            ->schema([
                                Placeholder::make('version_name')
                                    ->label('Version')
                                    ->content(function ($record) {
                                        return $record?->versionCountry?->version?->name ?? '-';
                                    })
                                    ->visible(function ($record) {
                                        return $record?->versionCountry != null;
                                    }),

                                Placeholder::make('country_name')
                                    ->label('Country')
                                    ->content(function ($record) {
                                        return $record?->versionCountry?->country?->name ?? '-';
                                    })
                                    ->visible(function ($record) {
                                        return $record?->versionCountry != null;
                                    }),

                                Select::make('language_id')
                                    ->relationship('language', 'name')
                                    ->required()
                                    ->default(function ($record) {
                                        return $record?->language_id;
                                    })
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
                                    ])->columnSpanFull(),
                            ]),
                    ])
                    ->action(function (VersionCountryLanguage $record) {
                        $newRecord = $record->replicate();
                        $newRecord->save();

                        Notification::make()
                            ->title('Record cloned successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultGroup(
                Tables\Grouping\Group::make('version_country_id') // Use the foreign key column for grouping
                    ->label('Version - Country')
                    ->getTitleFromRecordUsing(
                        fn(VersionCountryLanguage $record) => $record->versionCountry->version->name . ' - ' . $record->versionCountry->country->name
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
