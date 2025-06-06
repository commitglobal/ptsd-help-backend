<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RidForm
{
    public static function getToolsSchema(): Field
    {
        return
            Toggle::make('tools.rid')
                ->label('RID');
    }

    public static function getToolsResourcesSchema(): Field
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
                    if (!$versionCountryId)
                        return false;

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                    return $versionCountry?->tools['rid'] === true;
                });
    }
}
