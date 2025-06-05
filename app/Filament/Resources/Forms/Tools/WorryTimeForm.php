<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class WorryTimeForm
{
    public static function getToolsSchema(): Field
    {
        return
            Toggle::make('tools.worry-time')
                ->label('Worry time');
    }

    public static function getToolsResourcesSchema(): Field
    {
        return
            TextInput::make('tools.worry-time.categoryIcon')
                ->label('Worry time Category icon')
                ->url()
                ->visible(function ($livewire) {
                    return $livewire->getRecord()->versionCountry->tools['worry-time'] === true;
                });
    }
}
