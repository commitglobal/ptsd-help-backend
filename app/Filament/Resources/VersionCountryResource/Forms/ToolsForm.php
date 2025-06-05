<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms;

use App\Filament\Resources\VersionCountryResource\Forms\Tools\AmbientSoundsForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\MindfulnessForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\MyFeelingsForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\MyStrengthsForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\PauseForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\RecreationalActivitiesForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\RelationshipsForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\RidForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\SleepForm;
use App\Filament\Resources\VersionCountryResource\Forms\Tools\WorryTimeForm;
use Filament\Forms\Components\Section;

class ToolsForm
{
    public static function getToolsSchema(): array
    {
        return [
            ...RelationshipsForm::getToolsSchema(),
            AmbientSoundsForm::getToolsSchema(),
            ...MindfulnessForm::getToolsSchema(),
            PauseForm::getToolsSchema(),
            MyFeelingsForm::getToolsSchema(),
            WorryTimeForm::getToolsSchema(),
            RidForm::getToolsSchema(),
            ...RecreationalActivitiesForm::getToolsSchema(),
            ...SleepForm::getToolsSchema(),
            MyStrengthsForm::getToolsSchema(),
        ];
    }

    public static function getToolsResourcesSchema(): array
    {
        return [
            Section::make('Relationships')
                ->schema(RelationshipsForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Ambient sounds')
                ->schema(AmbientSoundsForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Mindfulness')
                ->schema(MindfulnessForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Pause')
                ->schema(PauseForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('My feelings')
                ->schema(MyFeelingsForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Worry time')
                ->schema(WorryTimeForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Rid')
                ->schema(RidForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Recreational activities')
                ->schema(RecreationalActivitiesForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('Sleep')
                ->schema(SleepForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),

            Section::make('My strengths')
                ->schema(MyStrengthsForm::getToolsResourcesSchema())
                ->collapsible()
                ->collapsed()
                ->compact(),
        ];
    }
}
