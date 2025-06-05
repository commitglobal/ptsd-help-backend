<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

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
                ->default(true)
                ->live(),

            Section::make('Subcategories')
                ->schema([
                    Toggle::make(name: 'tools.sleep.subcategories.sleep-help.enabled')
                        ->label('sleep-help')
                        ->default(true)
                        ->live(),

                    Toggle::make(name: 'tools.sleep.subcategories.sleep-habits.enabled')
                        ->label('sleep-habits')
                        ->default(true)
                        ->live(),

                    Toggle::make(name: 'tools.sleep.subcategories.sleep-perspective.enabled')
                        ->label('sleep-perspective')
                        ->default(true)
                        ->live(),
                ])
                ->compact()
                ->collapsible()
                ->visible(fn (callable $get) => $get('tools.sleep.enabled') === true),
        ];
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Section::make('sleep-help')
                ->schema([
                    Toggle::make(name: 'tools.sleep.subcategories.sleep-help.enabled')
                        ->label('sleep-help')
                        ->default(true)
                        ->live(),

                    TextInput::make('tools.sleep.subcategories.sleep-help.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn (callable $get) => $get('tools.sleep.subcategories.sleep-help.enabled') === true),
                ])
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('sleep-habits')
                ->schema([
                    Toggle::make(name: 'tools.sleep.subcategories.sleep-habits.enabled')
                        ->label('sleep-habits')
                        ->default(true)
                        ->live(),

                    TextInput::make('tools.sleep.subcategories.sleep-habits.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn (callable $get) => $get('tools.sleep.subcategories.sleep-habits.enabled') === true),
                ])
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('sleep-perspective')
                ->schema([
                    Toggle::make(name: 'tools.sleep.subcategories.sleep-perspective.enabled')
                        ->label('sleep-perspective')
                        ->default(true)
                        ->live(),

                    TextInput::make('tools.sleep.subcategories.sleep-perspective.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn (callable $get) => $get('tools.sleep.subcategories.sleep-perspective.enabled') === true),
                ])
                ->collapsible()
                ->collapsed()
                ->compact(),
        ];
    }
}
