<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SleepForm
{
    public static function getToolsSchema(): array
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
                ->visible(fn(callable $get) => $get('tools.sleep.enabled') === true),
        ];
    }

    public static function getToolsResourcesSchema(): Component
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
                            if (!$versionCountryId)
                                return false;

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
                            if (!$versionCountryId)
                                return false;

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
                            if (!$versionCountryId)
                                return false;

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
                    if (!$versionCountryId)
                        return false;

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                    return $versionCountry?->tools['sleep']['enabled'] === true;
                })
                ->collapsible()
                ->compact();
    }
}
