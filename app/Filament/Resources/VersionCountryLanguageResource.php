<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\VersionCountryLanguageResource\Pages;
use App\Models\VersionCountry;
use App\Models\Country;
use App\Models\Version;
use App\Models\VersionCountryLanguage;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class VersionCountryLanguageResource extends Resource
{
    protected static ?string $model = VersionCountryLanguage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->heading(heading: 'Content management')
                    ->description('Configure the available content for the current version/country/language')
                    ->schema([

                        Tabs::make('Form Tabs')
                            ->tabs([

                                Tabs\Tab::make('Manage > Symptoms')
                                    ->schema([
                                        Card::make()->schema([

                                        ]),
                                    ]),

                                Tabs\Tab::make('Manage > Tools')
                                    ->schema(VersionCountryResource\Forms\ToolsForm::getToolsResourcesSchema()),

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
                            ])
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
