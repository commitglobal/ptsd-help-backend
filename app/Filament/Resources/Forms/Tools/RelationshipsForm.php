<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RelationshipsForm
{
    public static function getSchema(): array
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
                ->visible(fn (callable $get) => $get('tools.relationships.enabled') === true),
        ];
    }

    public static function getMediaSchema(): Component
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
                            if (! $versionCountryId) {
                                return false;
                            }

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
                            if (! $versionCountryId) {
                                return false;
                            }

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
                            if (! $versionCountryId) {
                                return false;
                            }

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
                            if (! $versionCountryId) {
                                return false;
                            }

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
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['relationships']['enabled'] === true;
                })
                ->collapsible()
                ->compact();
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.relationships.title')
                ->label('Title')
                ->required(),

            TextArea::make('tools.relationships.description')
                ->label('Description')
                ->required(),

            Section::make('Common')
                ->schema([
                    TextInput::make('tools.relationships.common.start')
                        ->label('Start')
                        ->required(),

                    TextInput::make('tools.relationships.common.next')
                        ->label('Next')
                        ->required(),

                    TextInput::make('tools.relationships.common.previous')
                        ->label('Previous')
                        ->required(),

                    TextInput::make('tools.relationships.common.finish')
                        ->label('Finish')
                        ->required(),

                    TextInput::make('tools.relationships.common.save')
                        ->label('Finish')
                        ->required(),

                    TextInput::make('tools.relationships.common.cancel')
                        ->label('Finish')
                        ->required(),
                ])
                ->collapsible()
                ->compact(),

            Section::make('I-Messages')
                ->schema([
                    TextInput::make('tools.relationships.i-messages.title')
                        ->label('Title')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.add-message')
                        ->label('Add message')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.find-time')
                        ->label('Find time')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.text')
                        ->label('Text')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.when')
                        ->label('When')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.feel')
                        ->label('Feel')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.because')
                        ->label('Because')
                        ->required(),

                    TextInput::make('tools.relationships.i-messages.because')
                        ->label('Because')
                        ->required(),

                    Fieldset::make('New message')
                        ->schema([
                            TextInput::make('tools.relationships.i-messages.new-message.title')
                                ->label('Title')
                                ->required(),

                            TextInput::make('tools.relationships.i-messages.new-message.save')
                                ->label('Save')
                                ->required(),

                            Fieldset::make('Annoyance')
                                ->schema([
                                    TextInput::make('tools.relationships.i-messages.annoyance.label')
                                        ->label('Label')
                                        ->required(),

                                    TextInput::make('tools.relationships.i-messages.annoyance.placeholder')
                                        ->label('Placeholder')
                                        ->required(),

                                    TextInput::make('tools.relationships.i-messages.annoyance.example')
                                        ->label('Example')
                                        ->required(),

                                ]),

                            TextInput::make('tools.relationships.i-messages.new-message.declaration')
                                ->label('Declaration')
                                ->required(),

                            Fieldset::make('I Feel')
                                ->schema([
                                    TextInput::make('tools.relationships.i-messages.i-feel.label')
                                        ->label('Label')
                                        ->required(),

                                    TextInput::make('tools.relationships.i-messages.i-feel.placeholder')
                                        ->label('Placeholder')
                                        ->required(),

                                    TextInput::make('tools.relationships.i-messages.i-feel.example')
                                        ->label('Example')
                                        ->required(),
                                ]),

                            Fieldset::make('Because input')
                                ->schema([
                                    TextInput::make('tools.relationships.i-messages.because-input.label')
                                        ->label('Label')
                                        ->required(),

                                    TextInput::make('tools.relationships.i-messages.because-input.placeholder')
                                        ->label('Placeholder')
                                        ->required(),

                                    TextInput::make('tools.relationships.i-messages.because-input.example')
                                        ->label('Example')
                                        ->required(),
                                ]),

                            TextInput::make('tools.relationships.i-messages.edit.title')
                                ->label('Edit title')
                                ->required(),
                        ]),
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
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['relationships']['relationships-i-messages'] === true;
                }),

            Section::make('Reconnect with partner')
                ->schema([
                    TextInput::make('tools.relationships.reconnect-with-partner.title')
                        ->label('Title')
                        ->required(),

                    TextArea::make('tools.relationships.reconnect-with-partner.helper')
                        ->label('Helper')
                        ->required(),

                    Repeater::make('tools.relationships.reconnect-with-partner.repeater')
                        ->label('Strategies')
                        ->schema(
                            [
                                TextInput::make('title')
                                    ->label('Stress level')
                                    ->required(),

                                TextInput::make('description')
                                    ->label('Description')
                                    ->required(),

                                TextInput::make('sms')
                                    ->label('SMS'),
                            ]
                        )
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add strategy')
                        ->minItems(1),

                ])
                ->collapsible()
                ->compact()
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['relationships']['relationships-reconnect-with-partner'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['relationships']['relationships-reconnect-with-partner'] === true;
                }),

            Section::make('Positive communication')
                ->schema([
                    TextInput::make('tools.relationships.positive-communication.title')
                        ->label('Title')
                        ->required(),

                    TextArea::make('tools.relationships.positive-communication.helper')
                        ->label('Helper')
                        ->required(),

                    Repeater::make('tools.relationships.positive-communication.repeater')
                        ->label('Strategies')
                        ->schema(
                            [
                                TextInput::make('title')
                                    ->label('Stress level')
                                    ->required(),

                                TextArea::make('description')
                                    ->label('Description')
                                    ->required(),
                            ]
                        )
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add strategy')
                        ->minItems(1),

                ])
                ->collapsible()
                ->compact()
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['relationships']['relationships-positive-communication'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['relationships']['relationships-positive-communication'] === true;
                }),

            Section::make('Healthy arguments')
                ->schema([
                    TextInput::make('tools.relationships.healthy-arguments.title')
                        ->label('Title')
                        ->required(),

                    TextArea::make('tools.relationships.healthy-arguments.helper')
                        ->label('Helper')
                        ->required(),

                    Repeater::make('tools.relationships.healthy-arguments.repeater')
                        ->label('Strategies')
                        ->schema(
                            [
                                TextInput::make('title')
                                    ->label('Stress level')
                                    ->required(),

                                TextArea::make('description')
                                    ->label('Description')
                                    ->required(),
                            ]
                        )
                        ->reorderable()
                        ->deletable()
                        ->addActionLabel('Add strategy')
                        ->minItems(1),

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
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['relationships']['relationships-healthy-arguments'] === true;
                }),
        ];
    }
}
