<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Tools;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MyFeelingsForm
{
    public static function getSchema(): array
    {
        return [
            Toggle::make('tools.my-feelings.enabled')
                ->label('My feelings')
                ->default(true)
                ->live(),

            TextInput::make('tools.my-feelings.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn (callable $get) => $get('tools.my-feelings.enabled') === true),
        ];
    }
}
