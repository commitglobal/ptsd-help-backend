<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RecreationalActivitiesForm
{
    public static function getToolsSchema(): array
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

    public static function getToolsResourcesSchema(): Component
    {
        return
            Section::make('Recreational activities resources')
                ->schema([
                    TextInput::make('tools.recreational-activities.categoryIcon')
                        ->label('Recreational activities category icon')
                        ->url(),

                    TextInput::make(name: 'tools.recreational-activities.recreational-activities-alone.categoryIcon')
                        ->label('Recreational activities alone category icon')
                        ->url()
                        ->visible(function ($livewire) {
                            return $livewire->getRecord()->versionCountry->tools['recreational-activities']['recreational-activities-alone'] === true;
                        }),

                    TextInput::make('tools.recreational-activities.recreational-activities-city.categoryIcon')
                        ->label('Recreational activities city category icon')
                        ->url()
                        ->visible(function ($livewire) {
                            return $livewire->getRecord()->versionCountry->tools['recreational-activities']['recreational-activities-city'] === true;
                        }),

                    TextInput::make('tools.recreational-activities.recreational-activities-nature.categoryIcon')
                        ->label('Recreational activities nature category icon')
                        ->url()
                        ->visible(function ($livewire) {
                            return $livewire->getRecord()->versionCountry->tools['recreational-activities']['recreational-activities-nature'] === true;
                        }),

                ])
                ->visible(function ($livewire) {
                    return $livewire->getRecord()->versionCountry->tools['recreational-activities']['enabled'] === true;
                });
    }
}
