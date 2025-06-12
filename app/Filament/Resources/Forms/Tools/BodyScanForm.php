<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class BodyScanForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.body-scan.title')->label('Title')->required(),
            Textarea::make('tools.body-scan.description')->label('Description')->required(),
            TextInput::make('tools.body-scan.action-btn-label')->label('Action btn label')->required(),
            TextInput::make('tools.body-scan.done')->label('Done')->required(),

            Group::make()
                ->label('Julia')
                ->schema([
                    TextInput::make('tools.body-scan.julia.title')->label('Title')->required(),
                    Textarea::make('tools.body-scan.julia.description')->label('Description')->required(),
                    TextInput::make('tools.body-scan.julia.action-btn-label')->label('Action btn label')->required(),
                    TextInput::make('tools.body-scan.julia.done')->label('Done')->required(),
                ]),

            Group::make()
                ->label('Robyn')
                ->schema([
                    TextInput::make('tools.body-scan.robyn.title')->label('Title')->required(),
                    Textarea::make('tools.body-scan.robyn.description')->label('Description')->required(),
                    TextInput::make('tools.body-scan.robyn.action-btn-label')->label('Action btn label')->required(),
                    TextInput::make('tools.body-scan.robyn.done')->label('Done')->required(),
                ]),
        ];
    }
}
