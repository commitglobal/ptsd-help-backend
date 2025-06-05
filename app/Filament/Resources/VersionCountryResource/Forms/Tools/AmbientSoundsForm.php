<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms\Tools;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class AmbientSoundsForm
{
    public static function getToolsSchema(): Field
    {
        return
            Toggle::make('tools.ambient-sounds')
                ->label('Ambient sounds');
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Toggle::make('tools.ambient-sounds.enabled')
                ->label('Ambient sounds')
                ->default(true)
                ->default(true),

            TextInput::make('tools.ambient-sounds.categoryIcon')
                ->label('Category icon')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.headerImage')
                ->label('Header image')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.BEACH.soundUri')
                ->label('BEACH soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.BIRDS.soundUri')
                ->label('BIRDS soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.COUNTRY_ROAD.soundUri')
                ->label('COUNTRY_ROAD soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.CRICKETS.soundUri')
                ->label('CRICKETS soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.DRIPPING_WATER.soundUri')
                ->label('DRIPPING_WATER soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.FOREST.soundUri')
                ->label('FOREST soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.FROGS.soundUri')
                ->label('FROGS soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.MARSH.soundUri')
                ->label('MARSH soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.PUBLIC_POOL.soundUri')
                ->label('PUBLIC_POOL soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.RAIN.soundUri')
                ->label('RAIN soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.RUNNING_WATER.soundUri')
                ->label('RUNNING_WATER soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.STREAM_WATER.soundUri')
                ->label('STREAM_WATER soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.WATERFALL.soundUri')
                ->label('WATERFALL soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),

            TextInput::make('tools.ambient-sounds.WIND.soundUri')
                ->label('WIND soundUri')
                ->url()
                ->visible(fn(callable $get) => $get('tools.ambient-sounds.enabled') === true),
        ];
    }
}
