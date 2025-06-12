<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RecreationalActivitiesForm
{
    public static function getSchema(): array
    {
        return [
            Toggle::make(name: 'tools.recreational-activities.enabled')
                ->label('Enable Recreational activities')
                ->live(),

            Section::make('Recreational activities Subcategories')
                ->schema([
                    Toggle::make(name: 'tools.recreational-activities.recreational-activities-alone')
                        ->label('Recreational activities alone'),

                    Toggle::make(name: 'tools.recreational-activities.recreational-activities-city')
                        ->label('Recreational activities city'),

                    Toggle::make(name: 'tools.recreational-activities.recreational-activities-nature')
                        ->label('Recreational activities nature'),
                ])
                ->compact()
                ->collapsible()
                ->visible(fn (callable $get) => $get('tools.recreational-activities.enabled') === true),
        ];
    }

    public static function getMediaSchema(): Component
    {
        return
            Section::make('Recreational activities resources')
                ->schema([
                    TextInput::make('tools.recreational-activities.categoryIcon')
                        ->label('Recreational activities category icon')
                        ->url()
                        ->required(),

                    TextInput::make(name: 'tools.recreational-activities.recreational-activities-alone.categoryIcon')
                        ->label('Recreational activities alone category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['recreational-activities']['recreational-activities-alone'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (! $versionCountryId) {
                                return false;
                            }

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                            return $versionCountry?->tools['recreational-activities']['recreational-activities-alone'] === true;
                        }),

                    TextInput::make('tools.recreational-activities.recreational-activities-city.categoryIcon')
                        ->label('Recreational activities city category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['recreational-activities']['recreational-activities-city'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (! $versionCountryId) {
                                return false;
                            }

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                            return $versionCountry?->tools['recreational-activities']['recreational-activities-city'] === true;
                        }),

                    TextInput::make('tools.recreational-activities.recreational-activities-nature.categoryIcon')
                        ->label('Recreational activities nature category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['recreational-activities']['recreational-activities-nature'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (! $versionCountryId) {
                                return false;
                            }

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                            return $versionCountry?->tools['recreational-activities']['recreational-activities-nature'] === true;
                        }),

                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['recreational-activities']['enabled'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['recreational-activities']['enabled'] === true;
                })
                ->collapsible()
                ->compact();
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.recreational-activities.label')
                ->label('Recreational activities category label')
                ->required(),

            Section::make()
                ->label('Recreational activities alone')
                ->schema([
                    TextInput::make('tools.recreational-activities-alone.label')
                        ->label('Label')
                        ->required(),

                    TextInput::make('tools.recreational-activities-alone.title')
                        ->label('Title')
                        ->required(),

                    Repeater::make('tools.recreational-activities-alone.repeater')
                        ->label('Activities')
                        ->simple(
                            TextInput::make('description')
                                ->label('Recreational activity')
                                ->required(),
                        )
                        ->defaultItems(3)
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add recreational activity')
                        ->minItems(1),

                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['recreational-activities']['recreational-activities-alone'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['recreational-activities']['recreational-activities-alone'] === true;
                }),

            Section::make()
                ->label('Recreational activities city')
                ->schema([
                    TextInput::make('tools.recreational-activities-city.title')
                        ->label('Title')
                        ->required(),

                    Repeater::make('tools.recreational-activities-city.repeater')
                        ->label('Activities')
                        ->simple(
                            TextInput::make('description')
                                ->label('Recreational activity')
                                ->required(),
                        )
                        ->defaultItems(3)
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add recreational activity')
                        ->minItems(1),

                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['recreational-activities']['recreational-activities-city'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['recreational-activities']['recreational-activities-city'] === true;
                }),

            Section::make()
                ->label('Recreational activities nature')
                ->schema([
                    TextInput::make('tools.recreational-activities-city.title')
                        ->label('Title')
                        ->required(),

                    Repeater::make('tools.recreational-activities-city.repeater')
                        ->label('Activities')
                        ->simple(
                            TextInput::make('description')
                                ->label('Recreational activity')
                                ->required(),
                        )
                        ->defaultItems(3)
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add recreational activity')
                        ->minItems(1),
                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['recreational-activities']['recreational-activities-nature'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['recreational-activities']['recreational-activities-nature'] === true;
                }),
        ];
    }
}
