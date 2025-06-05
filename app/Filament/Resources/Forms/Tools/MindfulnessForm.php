<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MindfulnessForm
{
    public static function getToolsSchema(): array
    {
        return
            [
                Toggle::make(name: 'tools.mindfulness.enabled')
                    ->label('Mindfulness')
                    ->live(),

                Section::make('Mindfulness Subcategories')
                    ->schema([
                        Toggle::make(name: 'tools.mindfulness.mindfulness-conscious-breathing')
                            ->label('Mindfulness conscious breathing'),

                        Toggle::make(name: 'tools.mindfulness.mindfulness-mindful-walking')
                            ->label('Mindfulness conscious breathing'),

                        Toggle::make(name: 'tools.mindfulness.mindfulness-emotional-discomfort')
                            ->label('Mindfulness conscious breathing'),

                        Toggle::make(name: 'tools.mindfulness.mindfulness-sense-awareness')
                            ->label('Mindfulness conscious breathing'),

                        Toggle::make(name: 'tools.mindfulness.mindfulness-loving-kindness')
                            ->label('Mindfulness conscious breathing'),
                    ])
                    ->compact()
                    ->collapsible()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

            ];
    }

    public static function getToolsResourcesSchema(): Component
    {
        return Section::make('Mindfulness resources')
            ->schema([
                TextInput::make('tools.mindfulness.categoryIcon')
                    ->label('Mindfulness Category icon')
                    ->url()
                    ->visible(fn (callable $get) => $get('tools.mindfulness.enabled') === true),

                Section::make('mindfulness-conscious-breathing')
                    ->schema([
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
            ])
            ->visible(fn (callable $get) => $get('tools.mindfulness.enabled.enabled') === true);
    }
}
