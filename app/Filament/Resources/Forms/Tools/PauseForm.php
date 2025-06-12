<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PauseForm
{
    public static function getSchema(): Field
    {
        return
            Toggle::make('tools.pause')
                ->label('Pause');
    }

    public static function getMediaSchema(): Field
    {
        return TextInput::make('tools.pause.categoryIcon')
            ->label('Pause category icon')
            ->url()
            ->required()
            ->visible(function ($get, $livewire) {
                // For existing records (edit)
                if ($record = $livewire->getRecord()) {
                    return $record->versionCountry?->tools['pause'] === true;
                }

                // For new records (create)
                $versionCountryId = $get('version_country_id');
                if (! $versionCountryId) {
                    return false;
                }

                $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                return $versionCountry?->tools['pause'] === true;
            });
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.pause.description')
                ->label('Description')
                ->required(),

            TextInput::make('tools.pause.action-btn-label')
                ->label('Action btn label')
                ->required(),

            TextInput::make('tools.pause.helper')
                ->label('Helper')
                ->required(),

            TextInput::make('tools.pause.take_break')
                ->label('Take a break')
                ->required(),

            Repeater::make('tools.pause.repeater')
                ->label('Stress scale')
                ->simple(
                    TextInput::make('label')
                        ->label('Break')
                        ->required(),
                )
                ->defaultItems(3)
                ->reorderable()
                ->deletable()
                ->addActionLabel('Add break')
                ->minItems(2),
        ];
    }
}
