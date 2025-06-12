<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MyStrengthsForm
{
    public static function getSchema(): Field
    {
        return
            Toggle::make('tools.my-strengths')
                ->label('My strengths');
    }

    public static function getMediaSchema(): Field
    {
        return
            TextInput::make('tools.my-strengths.categoryIcon')
                ->label('My strengths category item')
                ->url()
                ->required()
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['my-strengths'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['my-strengths'] === true;
                });
    }

    public static function getContentSchema(): array
    {
        return [
            // Titles
            TextInput::make('tools.my-strengths.title')
                ->label('Main Title')
                ->required(),

            TextInput::make('tools.my-strengths.title-edit')
                ->label('Edit Title')
                ->required(),

            Textarea::make('tools.my-strengths.description')
                ->label('Description Text')
                ->required(),

            TextInput::make('tools.my-strengths.add')
                ->label('Add Button')
                ->required(),

            TextInput::make('tools.my-strengths.done')
                ->label('Done Button')
                ->required(),

            TextInput::make('tools.my-strengths.strength-label')
                ->label('Strength Prompt')
                ->required(),

            TextInput::make('tools.my-strengths.placeholder')
                ->label('Text Input Placeholder')
                ->required(),

            TextInput::make('tools.my-strengths.pick-image')
                ->label('Image Selection Prompt')
                ->required(),

            TextInput::make('tools.my-strengths.take-picture')
                ->label('Take Picture Button')
                ->required(),

            TextInput::make('tools.my-strengths.pick-from-library')
                ->label('Library Button')
                ->required(),

            TextInput::make('tools.my-strengths.delete-image')
                ->label('Delete Image Button')
                ->required(),

            TextInput::make('tools.my-strengths.modify-image')
                ->label('Modify Image Button')
                ->required(),

            Textarea::make('tools.my-strengths.info')
                ->label('Examples Info')
                ->required(),

            TextInput::make('tools.my-strengths.edit')
                ->label('Edit Button')
                ->required(),

            TextInput::make('tools.my-strengths.delete')
                ->label('Delete Button')
                ->required(),
        ];
    }
}
