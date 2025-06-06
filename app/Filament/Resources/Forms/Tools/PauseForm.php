<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PauseForm
{
    public static function getToolsSchema(): Field
    {
        return
            Toggle::make('tools.pause')
                ->label('Pause');
    }

    public static function getToolsResourcesSchema(): Field
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
                if (!$versionCountryId)
                    return false;

                $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                return $versionCountry?->tools['pause'] === true;
            });
    }
}
