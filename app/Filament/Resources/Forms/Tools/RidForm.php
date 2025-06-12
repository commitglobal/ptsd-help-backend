<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RidForm
{
    public static function getSchema(): Field
    {
        return
            Toggle::make('tools.rid')
                ->label('RID');
    }

    public static function getMediaSchema(): Field
    {
        return
            TextInput::make('tools.rid.categoryIcon')
                ->label('RID Category icon')
                ->url()
                ->required()
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['rid'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (! $versionCountryId) {
                        return false;
                    }

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);

                    return $versionCountry?->tools['rid'] === true;
                });
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.rid.title')->label('Title')->required(),
            TextInput::make('tools.rid.start')->label('Start')->required(),
            TextInput::make('tools.rid.done')->label('Done')->required(),
            TextArea::make('tools.rid.description')->label('Description')->required(),
            TextInput::make('tools.rid.continue')->label('Continue')->required(),
            TextInput::make('tools.rid.relax')->label('Relax')->required(),
            TextInput::make('tools.rid.relaxation')->label('Relaxation')->required(),
            TextInput::make('tools.rid.r')->label('R')->required(),
            TextInput::make('tools.rid.i')->label('I')->required(),
            TextInput::make('tools.rid.d')->label('D')->required(),
            TextInput::make('tools.rid.rid-identify')->label('RID: Identify')->required(),
            TextInput::make('tools.rid.identify')->label('Identify')->required(),
            TextInput::make('tools.rid.identify-trigger')->label('Identify trigger')->required(),
            TextInput::make('tools.rid.breathe')->label('Breathe')->required(),
            TextInput::make('tools.rid.30-more')->label('30 more')->required(),
            TextArea::make('tools.rid.review')->label('Review')->required(),
            TextInput::make('tools.rid.trigger')->label('Trigger')->required(),
            TextInput::make('tools.rid.trigger-placeholder')->label('Trigger placeholder')->required(),
            TextInput::make('tools.rid.different')->label('Different')->required(),
            TextInput::make('tools.rid.placeholder')->label('Placeholder')->required(),
            TextInput::make('tools.rid.rid-decide')->label('RID: Decide')->required(),
            TextInput::make('tools.rid.decide-next')->label('Decide next')->required(),
            TextArea::make('tools.rid.final-step')->label('Final step')->required(),
            TextInput::make('tools.rid.what-will-you-decide')->label('What will you decide to do')->required(),
            TextInput::make('tools.rid.decision-placeholder')->label('Decision placeholder')->required(),
            TextInput::make('tools.rid.rid-summary')->label('RID summary')->required(),
            TextInput::make('tools.rid.triggered-how')->label('Triggered how')->required(),
            TextInput::make('tools.rid.different-situation')->label('Different situation')->required(),
            TextInput::make('tools.rid.you-decided')->label('You decided')->required(),
            TextInput::make('tools.rid.empty')->label('empty')->required(),
            TextInput::make('tools.rid.date')->label('Date')->required(),
            TextInput::make('tools.rid.delete')->label('Delete')->required(),
        ];
    }
}
