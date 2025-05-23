<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class PauseForm
{
    public static function getSchema(): array
    {
        return [
            Toggle::make('tools.pause.enabled')
                ->label('Pause')
                ->default(true)
                ->live(),

            TextInput::make('tools.pause.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn (callable $get) => $get('tools.pause.enabled') === true),

        ];
    }
}
