<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MyStrengthsForm
{
    public static function getToolsSchema(): Field
    {
        return
            Toggle::make('tools.my-strengths.enabled')
                ->label('My strengths')
                ->default(true)
                ->live();
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Toggle::make('tools.my-strengths.enabled')
                ->label('my-strengths')
                ->default(true)
                ->live(),

            TextInput::make('tools.my-strengths.categoryIcon')
                ->label('My strengths')
                ->url()
                ->visible(fn (callable $get) => $get('tools.my-strengths.enabled') === true),
        ];
    }
}
