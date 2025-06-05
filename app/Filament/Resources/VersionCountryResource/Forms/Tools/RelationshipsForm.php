<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

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
                        ->label('Healthy Arguments')
                ])
                ->compact()
                ->collapsible()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),
        ];
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Toggle::make('tools.relationships.enabled')
                ->label('Enable Relationships')
                ->default(true)
                ->default(true),

            TextInput::make('tools.relationships.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),

            // subcategories
            Section::make('Reconnect With Partner')
                ->schema([
                    Toggle::make('tools.relationships.subcategories.relationships-reconnect-with-partner.enabled')
                        ->label('Reconnect With Partner')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.relationships.subcategories.relationships-reconnect-with-partner.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.relationships.subcategories.relationships-reconnect-with-partner.enabled') === true),
                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),

            Section::make('Positive Communication')
                ->schema([
                    Toggle::make('tools.relationships.subcategories.relationships-positive-communication.enabled')
                        ->label('Positive Communication')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.relationships.subcategories.relationships-positive-communication.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.relationships.subcategories.relationships-positive-communication.enabled') === true),

                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),

            Section::make('I-Messages')
                ->schema([
                    Toggle::make('tools.relationships.subcategories.relationships-i-messages.enabled')
                        ->label('I-Messages')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.relationships.subcategories.relationships-i-messages.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.relationships.subcategories.relationships-i-messages.enabled') === true),

                    TextInput::make('tools.relationships.subcategories.relationships-i-messages.headerImage')
                        ->label('Header image')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.relationships.subcategories.relationships-i-messages.enabled') === true),

                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),

            Section::make('Healthy Arguments')
                ->schema([
                    Toggle::make('tools.relationships.subcategories.relationships-healthy-arguments.enabled')
                        ->label('Healthy Arguments')
                        ->default(true)
                        ->default(true),

                    TextInput::make('tools.relationships.subcategories.relationships-healthy-arguments.categoryIcon')
                        ->label('Category icon')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.relationships.subcategories.relationships-healthy-arguments.enabled') === true),

                    TextInput::make('tools.relationships.subcategories.relationships-healthy-arguments.headerImage')
                        ->label('Header image')
                        ->url()
                        ->visible(fn(callable $get) => $get('tools.relationships.subcategories.relationships-healthy-arguments.enabled') === true),
                ])
                ->collapsible()
                ->collapsed()
                ->compact()
                ->visible(fn(callable $get) => $get('tools.relationships.enabled') === true),
        ];
    }
}
