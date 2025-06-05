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
                ->visible(function ($livewire) {
                    return $livewire->getRecord()->versionCountry->tools['rid'] === true;
                });
    }
}
