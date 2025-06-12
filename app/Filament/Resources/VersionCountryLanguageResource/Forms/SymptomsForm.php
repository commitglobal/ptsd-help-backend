<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms;

use Filament\Forms\Components\TextInput;

class SymptomsForm
{
    public static function getMediaSchema(): array
    {
        return [
            TextInput::make('symptoms.reminderOfTrauma.categoryIcon')
                ->label('Reminder of trauma icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.avoidingTriggers.categoryIcon')
                ->label('Avoiding triggers icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.disconnectedFromPeople.categoryIcon')
                ->label('Disconnected from people icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.disconnectedFromReality.categoryIcon')
                ->label('Disconnected from reality icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.sadHopeless.categoryIcon')
                ->label('Sad hopeless icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.worriedAnxious.categoryIcon')
                ->label('Worried anxious icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.angry.categoryIcon')
                ->label('Angry icon')
                ->url()
                ->required(),

            TextInput::make('symptoms.sleepProblems.categoryIcon')
                ->label('Sleep problems icon')
                ->url()
                ->required(),
        ];
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.symptoms.reminded_of_trauma')
                ->label('Reminded of Trauma')
                ->required(),

            TextInput::make('tools.symptoms.avoiding_triggers')
                ->label('Avoiding Triggers')
                ->required(),

            TextInput::make('tools.symptoms.disconnected_from_people')
                ->label('Disconnected from People')
                ->required(),

            TextInput::make('tools.symptoms.disconnected_from_reality')
                ->label('Disconnected from Reality')
                ->required(),

            TextInput::make('tools.symptoms.sad_hopeless')
                ->label('Sad/Hopeless')
                ->required(),

            TextInput::make('tools.symptoms.worried_anxious')
                ->label('Worried/Anxious')
                ->required(),

            TextInput::make('tools.symptoms.angry')
                ->label('Angry')
                ->required(),

            TextInput::make('tools.symptoms.sleep_problems')
                ->label('Sleep Problems')
                ->required(),
        ];
    }
}
