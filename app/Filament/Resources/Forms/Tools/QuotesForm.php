<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class QuotesForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.quotess.title')->label('Title')->required(),
            TextInput::make('tools.quotess.done')->label('Done')->required(),

            Repeater::make('tools.quotess.repeater')
                ->label('Quotes')
                ->schema(
                    [
                        TextInput::make('title')
                            ->label('Author')
                            ->required(),
                        Textarea::make('description')
                            ->label('Quote')
                            ->required(),
                    ]
                )
                ->defaultItems(3)
                ->reorderable()
                ->deletable()
                ->addActionLabel('Add quote')
                ->minItems(1),
        ];
    }
}
