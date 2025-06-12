<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class MuscleRelaxationForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.muscle-relaxation.title')->label('Title')->required(),
            Textarea::make('tools.muscle-relaxation.description')->label('Description')->required(),
            TextInput::make('tools.muscle-relaxation.action-btn-label')->label('Action btn label')->required(),
            TextInput::make('tools.muscle-relaxation.done')->label('Done')->required(),
        ];
    }
}
