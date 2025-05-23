<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class WorryTimeForm
{
    public static function getSchema(): array
    {
        return [
            Toggle::make('tools.worry-time.enabled')
                ->label('Worry time')
                ->default(true)
                ->live(),

            TextInput::make('tools.worry-time.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn (callable $get) => $get('tools.worry-time.enabled') === true),
        ];
    }
}
