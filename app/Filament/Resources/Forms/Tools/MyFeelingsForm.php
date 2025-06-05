<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

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

    public static function getToolsResourcesSchema(): Field
    {
        return
            TextInput::make('tools.my-feelings.categoryIcon')
                ->label('My feelings Category icon')
                ->url()
                ->visible(function ($livewire) {
                    return $livewire->getRecord()->versionCountry->tools['my-feelings'] === true;
                });
    }
}
