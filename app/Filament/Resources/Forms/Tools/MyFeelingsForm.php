<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MyFeelingsForm
{
    public static function getSchema(): Field
    {
        return
            Toggle::make('tools.my-feelings')
                ->label('My feelings');
    }

    public static function getMediaSchema(): Field
    {
        return
            TextInput::make('tools.my-feelings.categoryIcon')
                ->label('My feelings Category icon')
                ->url()
                ->required()
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['my-feelings'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['my-feelings'] === true;
                });
    }

    public static function getContentSchema(): array
    {
        return [
            // Action Buttons
            TextInput::make('tools.my-feelings.done')
                ->label('Done Button')
                ->required(),
            TextInput::make('tools.my-feelings.next')
                ->label('Next Button')
                ->required(),
            TextInput::make('tools.my-feelings.delete')
                ->label('Delete Button')
                ->required(),

            // Main Actions
            TextInput::make('tools.my-feelings.main-action-label')
                ->label('Main Action Button')
                ->required(),

            // Empty States
            TextInput::make('tools.my-feelings.no-feelings')
                ->label('No Feelings Message')
                ->required(),

            // Selection Prompts
            TextInput::make('tools.my-feelings.choose-main-feelings')
                ->label('Main Feelings Prompt')
                ->required(),
            TextInput::make('tools.my-feelings.choose-secondary-feelings')
                ->label('Secondary Feelings Prompt')
                ->required(),

            // Discomfort Meter
            TextInput::make('tools.my-feelings.discomfort-meter')
                ->label('Discomfort Meter Title')
                ->required(),

            TextInput::make('tools.my-feelings.discomfort-intensity')
                ->label('Discomfort Intensity Prompt')
                ->required(),

            TextInput::make('tools.my-feelings.stress')
                ->label('Stress Label')
                ->required(),

            Repeater::make('tools.my-feelings.repeater')
                ->label('Stress scale')
                ->simple(
                    TextInput::make('label')
                        ->label('Stress level')
                        ->required(),
                )
                ->defaultItems(3)
                ->reorderable()
                ->deletable()
                ->addActionLabel('Add stress Level')
                ->minItems(1),

            // Summary Section
            TextInput::make('tools.my-feelings.feelings-summary')
                ->label('Summary Title')
                ->required(),
            TextInput::make('tools.my-feelings.feelings-summary-description')
                ->label('Summary Description')
                ->required(),
            TextInput::make('tools.my-feelings.my-feelings')
                ->label('My Feelings Label')
                ->required(),
            TextInput::make('tools.my-feelings.emotion-intensity')
                ->label('Emotion Intensity Label')
                ->required(),
            TextInput::make('tools.my-feelings.ok')
                ->label('OK Button')
                ->required(),

        ];
    }
}
