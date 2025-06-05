<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Forms;

use App\Filament\Resources\Forms\Tools\AmbientSoundsForm;
use App\Filament\Resources\Forms\Tools\MindfulnessForm;
use App\Filament\Resources\Forms\Tools\MyFeelingsForm;
use App\Filament\Resources\Forms\Tools\MyStrengthsForm;
use App\Filament\Resources\Forms\Tools\PauseForm;
use App\Filament\Resources\Forms\Tools\RecreationalActivitiesForm;
use App\Filament\Resources\Forms\Tools\RelationshipsForm;
use App\Filament\Resources\Forms\Tools\RidForm;
use App\Filament\Resources\Forms\Tools\SleepForm;
use App\Filament\Resources\Forms\Tools\WorryTimeForm;

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
            RelationshipsForm::getToolsResourcesSchema(),
            AmbientSoundsForm::getToolsResourcesSchema(),
            MindfulnessForm::getToolsResourcesSchema(),
            PauseForm::getToolsResourcesSchema(),
            MyFeelingsForm::getToolsResourcesSchema(),
            WorryTimeForm::getToolsResourcesSchema(),
            RidForm::getToolsResourcesSchema(),
            RecreationalActivitiesForm::getToolsResourcesSchema(),
            SleepForm::getToolsResourcesSchema(),
            MyStrengthsForm::getToolsResourcesSchema(),
        ];
    }
}
