<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
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

    public static function getToolsResourcesSchema(): Component
    {
        return
            Section::make('Ambient sounds resources')
                ->schema([
                    TextInput::make('tools.ambient-sounds.categoryIcon')
                        ->label('Ambient sounds Category icon')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.headerImage')
                        ->label('Header image')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.BEACH.soundUri')
                        ->label('BEACH soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.BIRDS.soundUri')
                        ->label('BIRDS soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.COUNTRY_ROAD.soundUri')
                        ->label('COUNTRY_ROAD soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.CRICKETS.soundUri')
                        ->label('CRICKETS soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.DRIPPING_WATER.soundUri')
                        ->label('DRIPPING_WATER soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.FOREST.soundUri')
                        ->label('FOREST soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.FROGS.soundUri')
                        ->label('FROGS soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.MARSH.soundUri')
                        ->label('MARSH soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.PUBLIC_POOL.soundUri')
                        ->label('PUBLIC_POOL soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.RAIN.soundUri')
                        ->label('RAIN soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.RUNNING_WATER.soundUri')
                        ->label('RUNNING_WATER soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.STREAM_WATER.soundUri')
                        ->label('STREAM_WATER soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.WATERFALL.soundUri')
                        ->label('WATERFALL soundUri')
                        ->url()
                        ->required(),

                    TextInput::make('tools.ambient-sounds.WIND.soundUri')
                        ->label('WIND soundUri')
                        ->url()
                        ->required(),
                ])
                ->visible(function ($get, $livewire) {
                    // For existing records (edit)
                    if ($record = $livewire->getRecord()) {
                        return $record->versionCountry?->tools['ambient-sounds'] === true;
                    }

                    // For new records (create)
                    $versionCountryId = $get('version_country_id');
                    if (!$versionCountryId)
                        return false;

                    $versionCountry = \App\Models\VersionCountry::find($versionCountryId);
                    return $versionCountry?->tools['ambient-sounds'] === true;
                })
                ->collapsible()
                ->compact();
    }
}
