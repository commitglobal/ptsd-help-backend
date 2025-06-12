<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ConnectWithOthersForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.connect-with-others.title')->label('Title')->required(),
            TextInput::make('tools.connect-with-others.done')->label('Done')->required(),
            Textarea::make('tools.connect-with-others.static-text')->label('Static text')->required(),

            Repeater::make('tools.connect-with-others.repeater')
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
