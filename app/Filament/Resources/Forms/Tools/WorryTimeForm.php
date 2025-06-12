<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class WorryTimeForm
{
    public static function getSchema(): Field
    {
        return
            Toggle::make('tools.worry-time')
                ->label('Worry time');
    }

    public static function getMediaSchema(): Field
    {
        return
            TextInput::make('tools.worry-time.categoryIcon')
                ->label('Worry time Category icon')
                ->url()
                ->required()
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['worry-time'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['worry-time'] === true;
                });
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.worry-time.title')
                ->label('Title')
                ->required(),

            TextInput::make('tools.worry-time.help')
                ->label('Help')
                ->required(),

            TextArea::make('tools.worry-time.description')
                ->label('Description')
                ->required(),

            TextInput::make('tools.worry-time.write-here')
                ->label('Placeholder')
                ->required(),

            TextInput::make('tools.worry-time.subjects-to-think-about')
                ->label('Subjects to think about')
                ->required(),

            TextArea::make('tools.worry-time.help-text')
                ->label('Helptext')
                ->required(),

            TextInput::make('tools.worry-time.reminder')
                ->label('Reminder')
                ->required(),

            TextInput::make('tools.worry-time.daily-reminder')
                ->label('Daily reminder')
                ->required(),

            TextInput::make('tools.worry-time.ptsd-help')
                ->label('PTSD Help')
                ->required(),

            TextInput::make('tools.worry-time.review')
                ->label('Review')
                ->required(),
        ];
    }
}
