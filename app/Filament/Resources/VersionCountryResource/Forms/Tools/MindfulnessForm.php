<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MindfulnessForm
{
    public static function getToolsSchema(): array
    {
        return
            [
                Toggle::make('tools.mindfulness.enabled')
                    ->label('Mindfulness')
                    ->default(true)
                    ->live(),
                Section::make('Mindfulness Subcategories')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-conscious-breathing.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-mindful-walking.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-emotional-discomfort.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-sense-awareness.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-loving-kindness.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),
                    ])
                    ->compact()
                    ->collapsible()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

            ];
    }

    public static function getToolsResourcesSchema(): array
    {
        return
            [
                Toggle::make('tools.mindfulness.enabled')
                    ->label('Mindfulness')
                    ->default(true)
                    ->live(),

                TextInput::make('tools.mindfulness.categoryIcon')
                    ->label('Category icon')
                    ->url()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

                Section::make('mindfulness-conscious-breathing')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-conscious-breathing.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-conscious-breathing.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-conscious-breathing.enabled') === true),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-conscious-breathing.soundURI')
                            ->label('mindfulness-conscious-breathing sound uri')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-conscious-breathing.enabled') === true),

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

                Section::make('mindfulness-mindful-walking')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-mindful-walking.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-mindful-walking.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-mindful-walking.enabled') === true),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-mindful-walking.soundURI')
                            ->label('mindfulness-mindful-walking sound uri')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-mindful-walking.enabled') === true),

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

                Section::make('mindfulness-emotional-discomfort')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-emotional-discomfort.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-emotional-discomfort.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-emotional-discomfort.enabled') === true),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-emotional-discomfort.soundURI')
                            ->label('mindfulness-emotional-discomfort sound uri')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-emotional-discomfort.enabled') === true),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

                Section::make('mindfulness-sense-awareness')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-sense-awareness.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-sense-awareness.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-sense-awareness.enabled') === true),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-sense-awareness.soundURI')
                            ->label('mindfulness-sense-awareness sound uri')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-sense-awareness.enabled') === true),

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

                Section::make('mindfulness-loving-kindness')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.subcategories.mindfulness-loving-kindness.enabled')
                            ->label('Mindfulness conscious breathing')
                            ->default(true)
                            ->live(),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-loving-kindness.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-loving-kindness.enabled') === true),

                        TextInput::make('tools.mindfulness.subcategories.mindfulness-loving-kindness.soundURI')
                            ->label('mindfulness-loving-kindness sound uri')
                            ->url()
                            ->visible(fn (callable $get) => $get('tools.mindfulness.subcategories.mindfulness-loving-kindness.enabled') === true),

                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),
            ];
    }
}
