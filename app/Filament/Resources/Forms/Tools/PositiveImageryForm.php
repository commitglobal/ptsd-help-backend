<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class PositiveImageryForm
{
    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.positive-imagery.title')->label('Title')->required(),
            Textarea::make('tools.positive-imagery.description')->label('Description')->required(),
            TextInput::make('tools.positive-imagery.action-btn-label')->label('Action btn label')->required(),
            TextInput::make('tools.positive-imagery.done')->label('Done')->required(),

            Group::make()
                ->label('Beach')
                ->schema([
                    TextInput::make('tools.body-scan.beach.title')->label('Title')->required(),
                    Textarea::make('tools.body-scan.beach.description')->label('Description')->required(),
                    TextInput::make('tools.body-scan.beach.action-btn-label')->label('Action btn label')->required(),
                    TextInput::make('tools.body-scan.beach.done')->label('Done')->required(),
                ]),

            Group::make()
                ->label('Country road')
                ->schema([
                    TextInput::make('tools.body-scan.country-road.title')->label('Title')->required(),
                    Textarea::make('tools.body-scan.country-road.description')->label('Description')->required(),
                    TextInput::make('tools.body-scan.country-road.action-btn-label')->label('Action btn label')->required(),
                    TextInput::make('tools.body-scan.country-road.done')->label('Done')->required(),
                ]),

            Group::make()
                ->label('Forest')
                ->schema([
                    TextInput::make('tools.body-scan.forest.title')->label('Title')->required(),
                    Textarea::make('tools.body-scan.forest.description')->label('Description')->required(),
                    TextInput::make('tools.body-scan.forest.action-btn-label')->label('Action btn label')->required(),
                    TextInput::make('tools.body-scan.forest.done')->label('Done')->required(),
                ]),

            Section::make()
                ->label('Observe thoughts')
                ->schema([
                    TextInput::make('tools.body-scan.observe-thoughts.title')->label('Title')->required(),
                    Textarea::make('tools.body-scan.observe-thoughts.description')->label('Description')->required(),
                    TextInput::make('tools.body-scan.observe-thoughts.action-btn-label')->label('Action btn label')->required(),
                    TextInput::make('tools.body-scan.observe-thoughts.done')->label('Done')->required(),

                    Group::make()
                        ->label('Clouds')
                        ->schema([
                            TextInput::make('tools.body-scan.observe-thoughts.clouds.title')->label('Title')->required(),
                            Textarea::make('tools.body-scan.observe-thoughts.clouds.description')->label('Description')->required(),
                            TextInput::make('tools.body-scan.observe-thoughts.clouds.action-btn-label')->label('Action btn label')->required(),
                            TextInput::make('tools.body-scan.observe-thoughts.clouds.done')->label('Done')->required(),
                        ]),

                    Group::make()
                        ->label('River')
                        ->schema([
                            TextInput::make('tools.body-scan.observe-thoughts.river.title')->label('Title')->required(),
                            Textarea::make('tools.body-scan.observe-thoughts.river.description')->label('Description')->required(),
                            TextInput::make('tools.body-scan.observe-thoughts.river.action-btn-label')->label('Action btn label')->required(),
                            TextInput::make('tools.body-scan.observe-thoughts.river.done')->label('Done')->required(),
                        ]),

                ]),
        ];
    }
}
