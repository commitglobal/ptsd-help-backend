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
                    ->visible(fn(callable $get) => $get('tools.mindfulness.enabled') === true),

            ];
    }

    public static function getToolsResourcesSchema(): Component
    {
        return Section::make('Mindfulness resources')
            ->schema([
                TextInput::make('tools.mindfulness.categoryIcon')
                    ->label('Mindfulness Category icon')
                    ->url()
                    ->required()
                    ->visible(function ($get, $livewire) {
                        // For existing records (edit)
                        if ($record = $livewire->getRecord()) {
                            return $record->versionCountry?->tools['mindfulness']['enabled'] === true;
                        }

                        // For new records (create)
                        $versionCountryId = $get('version_country_id');
                        if (!$versionCountryId)
                            return false;

                        $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                        return $versionCountry?->tools['mindfulness']['enabled'] === true;
                    }),

                Section::make('Conscious Breathing')
                    ->schema([
                        TextInput::make('tools.mindfulness.mindfulness-conscious-breathing.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->required(),

                        TextInput::make('tools.mindfulness.mindfulness-conscious-breathing.soundURI')
                            ->label('Sound uri')
                            ->url()
                            ->required()
                    ])
                    ->collapsible()
                    ->compact()
                    ->visible(function ($get, $livewire) {
                        // For existing records (edit)
                        if ($record = $livewire->getRecord()) {
                            return $record->versionCountry?->tools['mindfulness']['mindfulness-conscious-breathing'] === true;
                        }

                        // For new records (create)
                        $versionCountryId = $get('version_country_id');
                        if (!$versionCountryId)
                            return false;

                        $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                        return $versionCountry?->tools['mindfulness']['mindfulness-conscious-breathing'] === true;
                    }),

                Section::make('Mindful walking')
                    ->schema([
                        TextInput::make('tools.mindfulness.mindfulness-mindful-walking.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->required(),

                        TextInput::make('tools.mindfulness.mindfulness-mindful-walking.soundURI')
                            ->label('Sound uri')
                            ->url()
                            ->required()
                    ])
                    ->collapsible()
                    ->compact()
                    ->visible(function ($get, $livewire) {
                        // For existing records (edit)
                        if ($record = $livewire->getRecord()) {
                            return $record->versionCountry?->tools['mindfulness']['mindfulness-mindful-walking'] === true;
                        }

                        // For new records (create)
                        $versionCountryId = $get('version_country_id');
                        if (!$versionCountryId)
                            return false;

                        $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                        return $versionCountry?->tools['mindfulness']['mindfulness-mindful-walking'] === true;
                    }),

                Section::make('Emotional Discomfort')
                    ->schema([
                        TextInput::make('tools.mindfulness.mindfulness-emotional-discomfort.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->required(),

                        TextInput::make('tools.mindfulness.mindfulness-emotional-discomfort.soundURI')
                            ->label('Sound uri')
                            ->url()
                            ->required()
                    ])
                    ->collapsible()
                    ->compact()
                    ->visible(function ($get, $livewire) {
                        // For existing records (edit)
                        if ($record = $livewire->getRecord()) {
                            return $record->versionCountry?->tools['mindfulness']['mindfulness-emotional-discomfort'] === true;
                        }

                        // For new records (create)
                        $versionCountryId = $get('version_country_id');
                        if (!$versionCountryId)
                            return false;

                        $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                        return $versionCountry?->tools['mindfulness']['mindfulness-emotional-discomfort'] === true;
                    }),

                Section::make('Sense Awareness')
                    ->schema([
                        TextInput::make('tools.mindfulness.mindfulness-sense-awareness.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->required(),

                        TextInput::make('tools.mindfulness.mindfulness-sense-awareness.soundURI')
                            ->label('Sound uri')
                            ->url()
                            ->required()
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->visible(function ($get, $livewire) {
                        // For existing records (edit)
                        if ($record = $livewire->getRecord()) {
                            return $record->versionCountry?->tools['mindfulness']['mindfulness-sense-awareness'] === true;
                        }

                        // For new records (create)
                        $versionCountryId = $get('version_country_id');
                        if (!$versionCountryId)
                            return false;

                        $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                        return $versionCountry?->tools['mindfulness']['mindfulness-sense-awareness'] === true;
                    }),

                Section::make('Mindfulness loving kindness')
                    ->schema([
                        TextInput::make('tools.mindfulness.mindfulness-loving-kindness.categoryIcon')
                            ->label('Category icon')
                            ->url()
                            ->required(),

                        TextInput::make('tools.mindfulness.mindfulness-loving-kindness.soundURI')
                            ->label('Sound uri')
                            ->url()
                            ->required()

                    ])
                    ->collapsible()
                    ->compact()
                    ->visible(function ($get, $livewire) {
                        // For existing records (edit)
                        if ($record = $livewire->getRecord()) {
                            return $record->versionCountry?->tools['mindfulness']['mindfulness-loving-kindness'] === true;
                        }

                        // For new records (create)
                        $versionCountryId = $get('version_country_id');
                        if (!$versionCountryId)
                            return false;

                        $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                        return $versionCountry?->tools['mindfulness']['mindfulness-loving-kindness'] === true;
                    })

            ])
            ->visible(function ($get, $livewire) {
                // For existing records (edit)
                if ($record = $livewire->getRecord()) {
                    return $record->versionCountry?->tools['mindfulness']['enabled'] === true;
                }

                // For new records (create)
                $versionCountryId = $get('version_country_id');
                if (!$versionCountryId)
                    return false;

                $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                return $versionCountry?->tools['mindfulness']['enabled'] === true;
            })
            ->collapsible()
            ->compact();
    }
}
