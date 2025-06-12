<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class DeepBreathingForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.deep-breathing.title')->label('Title')->required(),
            Textarea::make('tools.deep-breathing.description')->label('Description')->required(),
            TextInput::make('tools.deep-breathing.action-btn-label')->label('Action btn label')->required(),
            TextInput::make('tools.deep-breathing.done')->label('Done')->required(),
        ];
    }
}
