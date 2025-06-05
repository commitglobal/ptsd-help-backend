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
            ->visible(function ($livewire) {
                return $livewire->getRecord()->versionCountry->tools['pause'] === true;
            });
    }
}
