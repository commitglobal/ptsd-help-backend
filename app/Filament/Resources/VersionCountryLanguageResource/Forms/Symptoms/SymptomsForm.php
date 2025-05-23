<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Symptoms;
use Filament\Forms\Components\TextInput;

class SymptomsForm
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('symptoms.reminderOfTrauma.categoryIcon')
                ->label('Reminder of trauma icon')
                ->url(),

            TextInput::make('symptoms.avoidingTriggers.categoryIcon')
                ->label('Avoiding triggers icon')
                ->url(),

            TextInput::make('symptoms.disconnectedFromPeople.categoryIcon')
                ->label('Disconnected from people icon')
                ->url(),

            TextInput::make('symptoms.disconnectedFromReality.categoryIcon')
                ->label('Disconnected from reality icon')
                ->url(),

            TextInput::make('symptoms.sadHopeless.categoryIcon')
                ->label('Sad hopeless icon')
                ->url(),

            TextInput::make('symptoms.worriedAnxious.categoryIcon')
                ->label('Worried anxious icon')
                ->url(),

            TextInput::make('symptoms.angry.categoryIcon')
                ->label('Angry icon')
                ->url(),

            TextInput::make('symptoms.sleepProblems.categoryIcon')
                ->label('Sleep problems icon')
                ->url(),
        ];
    }
}