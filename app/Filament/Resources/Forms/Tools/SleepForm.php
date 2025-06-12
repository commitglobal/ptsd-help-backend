<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SleepForm
{
    public static function getSchema(): array
    {
        return [
            Toggle::make('tools.sleep.enabled')
                ->label('Sleep')
                ->live(),

            Section::make('Sleep Subcategories')
                ->schema([
                    Toggle::make(name: 'tools.sleep.sleep-help')
                        ->label('sleep-help'),

                    Toggle::make(name: 'tools.sleep.sleep-habits')
                        ->label('sleep-habits'),

                    Toggle::make(name: 'tools.sleep.sleep-perspective')
                        ->label('sleep-perspective'),
                ])
                ->compact()
                ->collapsible()
                ->visible(fn (callable $get) => $get('tools.sleep.enabled') === true),
        ];
    }

    public static function getMediaSchema(): Component
    {
        return
            Section::make('Sleep resources')
                ->schema([
                    TextInput::make('tools.sleep.categoryIcon')
                        ->label('Sleep category icon')
                        ->url()
                        ->required(),

                    TextInput::make('tools.sleep.sleep-help.categoryIcon')
                        ->label('Sleep help Category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['sleep']['sleep-help'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (! $versionCountryId) {
                                return false;
                            }

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                            return $versionCountry?->tools['sleep']['sleep-help'] === true;
                        }),

                    TextInput::make('tools.sleep.sleep-habits.categoryIcon')
                        ->label('Sleep habits Category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['sleep']['sleep-habits'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (! $versionCountryId) {
                                return false;
                            }

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                            return $versionCountry?->tools['sleep']['sleep-habits'] === true;
                        }),

                    TextInput::make('tools.sleep.sleep-perspective.categoryIcon')
                        ->label('Sleep perspective Category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['sleep']['sleep-perspective'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (! $versionCountryId) {
                                return false;
                            }

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                            return $versionCountry?->tools['sleep']['sleep-perspective'] === true;
                        }),
                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['sleep']['enabled'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['sleep']['enabled'] === true;
                })
                ->collapsible()
                ->compact();
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make(name: 'tools.sleep.title')
                ->label('Title'),

            Repeater::make('tools.sleep.repeater')
                ->label('Sleep help')
                ->simple(
                    TextInput::make('title')
                        ->label('Title')
                        ->required(),
                )
                ->defaultItems(3)
                ->reorderable()
                ->deletable()
                ->addActionLabel('Add method')
                ->minItems(1),

            Section::make('Sleep habits')
                ->schema([

                    TextInput::make('tools.sleep.sleep-habits.title')
                        ->label('Title')
                        ->required(),

                    TextArea::make('tools.sleep.sleep-habits.info')
                        ->label('Info')
                        ->required(),

                    TextArea::make('tools.sleep.sleep-habits.description')
                        ->label('Description')
                        ->required(),

                    TextInput::make('tools.sleep.sleep-habits.save')
                        ->label('Save')
                        ->required(),

                    Group::make()
                        ->schema([
                            TextInput::make('tools.sleep.sleep-habits.list.relaxing-activities')
                                ->label('Relaxing Activities')
                                ->required(),

                            TextInput::make('tools.sleep.sleep-habits.list.awake-activities')
                                ->label('Activities to Stay Awake')
                                ->required(),

                            TextInput::make('tools.sleep.sleep-habits.list.no-sleep-activities')
                                ->label("Activities When I Can't Sleep")
                                ->required(),

                            TextInput::make('tools.sleep.sleep-habits.list.wake-up-activities')
                                ->label('Activities to Help Me Wake Up')
                                ->required(),
                        ]),

                    Section::make('Relaxing activities')
                        ->schema([
                            TextArea::make('tools.sleep.sleep-habits.relaxing-activities.description')
                                ->label('Description')
                                ->required(),

                            TextArea::make('tools.sleep.sleep-habits.relaxing-activities.info')
                                ->label('Info')
                                ->required(),

                            TextInput::make('tools.sleep.sleep-habits.relaxing-activities.reminder-title')
                                ->label('Reminder Title')
                                ->required(),

                            TextInput::make('tools.sleep.sleep-habits.relaxing-activities.reminder-body')
                                ->label('Reminder Body')
                                ->required(),

                            TextInput::make('tools.sleep.sleep-habits.relaxing-activities.reminder-time')
                                ->label('Reminder Time')
                                ->required(),

                            Repeater::make('tools.sleep.sleep-habits.relaxing-activities.list')
                                ->label('Relaxing activities')
                                ->simple(
                                    TextInput::make('text')
                                        ->label('Activity')
                                        ->required(),
                                )
                                ->defaultItems(3)
                                ->reorderable()
                                ->deletable()
                                ->addActionLabel('Add activity')
                                ->minItems(1),
                        ]),

                    Section::make('Awake activities')
                        ->schema([
                            TextArea::make('tools.sleep.sleep-habits.awake-activities.description')
                                ->label('Description')
                                ->required(),

                            TextArea::make('tools.sleep.sleep-habits.awake-activities.info')
                                ->label('Info')
                                ->required(),

                            Repeater::make('tools.sleep.sleep-habits.awake-activities.list')
                                ->label('Awake activities')
                                ->simple(
                                    TextInput::make('text')
                                        ->label('Activity')
                                        ->required(),
                                )
                                ->defaultItems(3)
                                ->reorderable()
                                ->deletable()
                                ->addActionLabel('Add activity')
                                ->minItems(1),
                        ]),

                    Section::make('No sleep activities')
                        ->schema([
                            TextArea::make('tools.sleep.sleep-habits.no-sleep-activities.description')
                                ->label('Description')
                                ->required(),

                            TextArea::make('tools.sleep.sleep-habits.no-sleep-activities.info')
                                ->label('Info')
                                ->required(),

                            Repeater::make('tools.sleep.sleep-habits.no-sleep-activities.list')
                                ->label('No sleep activities')
                                ->simple(
                                    TextInput::make('text')
                                        ->label('Activity')
                                        ->required(),
                                )
                                ->defaultItems(3)
                                ->reorderable()
                                ->deletable()
                                ->addActionLabel('Add activity')
                                ->minItems(1),
                        ]),

                    Section::make('Wake up activities')
                        ->schema([
                            TextArea::make('tools.sleep.sleep-habits.wake-up-activities.description')
                                ->label('Description')
                                ->required(),

                            TextArea::make('tools.sleep.sleep-habits.wake-up-activities.info')
                                ->label('Info')
                                ->required(),

                            Repeater::make('tools.sleep.sleep-habits.wake-up-activities.list')
                                ->label('Wake up activities')
                                ->simple(
                                    TextInput::make('text')
                                        ->label('Activity')
                                        ->required(),
                                )
                                ->defaultItems(3)
                                ->reorderable()
                                ->deletable()
                                ->addActionLabel('Add activity')
                                ->minItems(1),
                        ]),
                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['sleep']['sleep-habits'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['sleep']['sleep-habits'] === true;
                }),

            Section::make('Sleep perspective')
                ->schema([
                    TextArea::make('tools.sleep.sleep-perspective.title')
                        ->label('Title')
                        ->required(),

                    TextArea::make('tools.sleep.sleep-perspective.description')
                        ->label('Description')
                        ->required(),

                    Repeater::make('tools.sleep.sleep-perspective.repeater')
                        ->label('Habits')
                        ->simple(
                            TextInput::make('title')
                                ->label('Habit')
                                ->required(),
                        )
                        ->defaultItems(3)
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add habit')
                        ->minItems(1),
                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['sleep']['sleep-perspective'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['sleep']['sleep-perspective'] === true;
                }),
        ];
    }
}
