<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class RidForm
{
    public static function getSchema(): array
    {
        return [
            Toggle::make('tools.rid.enabled')
                ->label('RID')
                ->default(true)
                ->live(),

            TextInput::make('tools.rid.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn (callable $get) => $get('tools.rid.enabled') === true),
        ];
    }
}
