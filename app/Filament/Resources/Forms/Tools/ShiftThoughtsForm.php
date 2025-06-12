<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ShiftThoughtsForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.shift-thoughts.title')->label('Title')->required(),
            Textarea::make('tools.shift-thoughts.helper')->label('Helper')->required(),
            TextInput::make('tools.shift-thoughts.start')->label('Start')->required(),

            Repeater::make('tools.shift-thoughts.repeater')
                ->label('Thoughts')
                ->simple(
                    TextInput::make('label')
                        ->label('Thought')
                        ->required(),
                )
                ->defaultItems(3)
                ->reorderable()
                ->deletable()
                ->addActionLabel('Add to thoughts')
                ->minItems(1),
        ];
    }
}
