<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class MyFeelingsForm
{
    public static function getToolsSchema(): Field
    {
        return
            Toggle::make('tools.my-feelings')
                ->label('My feelings');
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Toggle::make('tools.my-feelings.enabled')
                ->label('My feelings')
                ->default(true)
                ->default(true),

            TextInput::make('tools.my-feelings.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn(callable $get) => $get('tools.my-feelings.enabled') === true),
        ];
    }
}
