<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RelationshipsForm
{
    public static function getToolsSchema(): array
    {
        return [
            Toggle::make('tools.relationships.enabled')
                ->label('Enable Relationships')
                ->live(),

            Section::make('Relationships Subcategories')
                ->schema([
                    Toggle::make('tools.relationships.relationships-reconnect-with-partner')
                        ->label('Reconnect With Partner'),

                    Toggle::make('tools.relationships.relationships-positive-communication')
                        ->label('Positive Communication'),

                    Toggle::make('tools.relationships.relationships-i-messages')
                        ->label('I-Messages'),

                    Toggle::make('tools.relationships.relationships-healthy-arguments')
                        ->label('Healthy Arguments'),
                ])
                ->compact()
                ->collapsible()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),
        ];
    }

    public static function getToolsResourcesSchema(): Component
    {
        return
            Section::make('Relationships Category resources')
                ->schema([
                    TextInput::make(name: 'tools.relationships.categoryIcon')
                        ->label('Relationships Category icon')
                        ->url()
                        ->required(),

                    TextInput::make('tools.relationships.relationships-reconnect-with-partner.categoryIcon')
                        ->label('Reconnect With Partner Category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['relationships']['relationships-reconnect-with-partner'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (!$versionCountryId)
                                return false;

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                            return $versionCountry?->tools['relationships']['relationships-reconnect-with-partner'] === true;
                        }),

                    TextInput::make('tools.relationships.relationships-positive-communication.categoryIcon')
                        ->label('Positive Communication Category icon')
                        ->url()
                        ->required()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['relationships']['relationships-positive-communication'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (!$versionCountryId)
                                return false;

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                            return $versionCountry?->tools['relationships']['relationships-positive-communication'] === true;
                        }),

                    Section::make('I-Messages')
                        ->schema([
                            TextInput::make('tools.relationships.relationships-i-messages.categoryIcon')
                                ->label('Category icon')
                                ->url()
                                ->required(),

                            TextInput::make('tools.relationships.relationships-i-messages.headerImage')
                                ->label('Header image')
                                ->url()
                                ->required(),
                        ])
                        ->collapsible()
                        ->compact()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['relationships']['relationships-i-messages'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (!$versionCountryId)
                                return false;

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                            return $versionCountry?->tools['relationships']['relationships-i-messages'] === true;
                        }),

                    Section::make('Healthy Arguments')
                        ->schema([
                            TextInput::make('tools.relationships.relationships-healthy-arguments.categoryIcon')
                                ->label('Category icon')
                                ->url()
                                ->required(),

                            TextInput::make('tools.relationships.relationships-healthy-arguments.headerImage')
                                ->label('Header image')
                                ->url()
                                ->required(),
                        ])
                        ->collapsible()
                        ->compact()
                        ->visible(function ($get, $livewire) {
                            // For existing records (edit)
                            if ($record = $livewire->getRecord()) {
                                return $record->versionCountry?->tools['relationships']['relationships-healthy-arguments'] === true;
                            }

                            // For new records (create)
                            $versionCountryId = $get('version_country_id');
                            if (!$versionCountryId)
                                return false;

                            $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                            return $versionCountry?->tools['relationships']['relationships-healthy-arguments'] === true;
                        }),

                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['relationships']['enabled'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (!$versionCountryId)
                        return false;

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                    return $versionCountry?->tools['relationships']['enabled'] === true;
                })
                ->collapsible()
                ->compact();
    }
}
