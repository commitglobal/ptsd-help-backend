<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

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
                        ->label('recreational-activities-alone'),

                    Toggle::make(name: 'tools.recreational-activities.recreational-activities-city')
                        ->label('recreational-activities-city'),

                    Toggle::make(name: 'tools.recreational-activities.recreational-activities-nature')
                        ->label('recreational-activities-nature'),
                ])
                ->compact()
                ->collapsible()
                ->visible(fn(callable $get) => $get('tools.recreational-activities.enabled') === true),
        ];
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Toggle::make('tools.recreational-activities.enabled')
                ->label('Enable Recreational activities')
                ->default(true)
                ->default(true),

            Section::make('recreational-activities-alone')
                ->schema([
                    Toggle::make(name: 'tools.recreational-activities.subcategories.recreational-activities-alone.enabled')
                        ->label('recreational-activities-alone')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.recreational-activities.subcategories.recreational-activities-alone.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.recreational-activities.subcategories.recreational-activities-alone.enabled') === true),

                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.recreational-activities.enabled') === true),

            Section::make('recreational-activities-city')
                ->schema([
                    Toggle::make(name: 'tools.recreational-activities.subcategories.recreational-activities-city.enabled')
                        ->label('recreational-activities-city')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.recreational-activities.subcategories.recreational-activities-city.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.recreational-activities.subcategories.recreational-activities-city.enabled') === true),

                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.recreational-activities.enabled') === true),

            Section::make('recreational-activities-nature')
                ->schema([
                    Toggle::make(name: 'tools.recreational-activities.subcategories.recreational-activities-nature.enabled')
                        ->label('recreational-activities-nature')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.recreational-activities.subcategories.recreational-activities-nature.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.recreational-activities.subcategories.recreational-activities-nature.enabled') === true),
                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.recreational-activities.enabled') === true),
        ];
    }
}
