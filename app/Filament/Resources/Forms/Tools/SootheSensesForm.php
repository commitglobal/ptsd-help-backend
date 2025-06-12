<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SootheSensesForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.soothe-senses.title')->label('Title')->required(),
            TextInput::make('tools.soothe-senses.done')->label('Done')->required(),
            Repeater::make('tools.soothe-senses.repeater')
                ->label('Soothe senses tools')
                ->schema(
                    [
                        TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        Textarea::make('description')
                            ->label('Title')
                            ->required(),
                    ]
                )
                ->defaultItems(3)
                ->reorderable()
                ->deletable()
                ->addActionLabel('Add method')
                ->minItems(1),

        ];
    }
}
