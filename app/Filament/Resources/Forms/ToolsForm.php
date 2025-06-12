<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms;

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
use Filament\Forms\Components\TextInput;

class ToolsForm
{
    public static function getToolsSchema(): array
    {
        return [
            ...RelationshipsForm::getSchema(),
            AmbientSoundsForm::getSchema(),
            ...MindfulnessForm::getSchema(),
            PauseForm::getSchema(),
            MyFeelingsForm::getSchema(),
            WorryTimeForm::getSchema(),
            RidForm::getSchema(),
            ...RecreationalActivitiesForm::getSchema(),
            ...SleepForm::getSchema(),
            MyStrengthsForm::getSchema(),
        ];
    }

    public static function getToolsMediaSchema(): array
    {
        return [
            RelationshipsForm::getMediaSchema(),
            AmbientSoundsForm::getMediaSchema(),
            MindfulnessForm::getMediaSchema(),
            PauseForm::getMediaSchema(),
            MyFeelingsForm::getMediaSchema(),
            WorryTimeForm::getMediaSchema(),
            RidForm::getMediaSchema(),
            RecreationalActivitiesForm::getMediaSchema(),
            SleepForm::getMediaSchema(),
            MyStrengthsForm::getMediaSchema(),
        ];
    }

    public static function getContentSchema(): array
    {
        return [
            TextInput::make('tools.list.relationships')
                ->label('Relationships')
                ->required(),

            TextInput::make('tools.list.reconnect-with-partner')
                ->label('Reconnect with your partner')
                ->required(),

            TextInput::make('tools.list.positive-communication')
                ->label('Positive Communication')
                ->required(),

            TextInput::make('tools.list.healthy-arguments')
                ->label('Healthy Arguments')
                ->required(),

            TextInput::make('tools.list.i-messages')
                ->label('I Messages')
                ->required(),

            TextInput::make('tools.list.mindfulness')
                ->label('Mindfulness')
                ->required(),

            TextInput::make('tools.list.ambient-sounds')
                ->label('Ambient Sounds')
                ->required(),

            TextInput::make('tools.list.conscious-breathing')
                ->label('Conscious Breathing')
                ->required(),

            TextInput::make('tools.list.mindful-walking')
                ->label('Mindful Walking')
                ->required(),

            TextInput::make('tools.list.emotional-discomfort')
                ->label('Emotional Discomfort')
                ->required(),

            TextInput::make('tools.list.sense-awareness')
                ->label('Sense Awareness')
                ->required(),

            TextInput::make('tools.list.loving-kindness')
                ->label('Loving Kindness')
                ->required(),

            TextInput::make('tools.list.my-feelings')
                ->label('My Feelings')
                ->required(),

            TextInput::make('tools.list.sleep')
                ->label('Sleep Instruments')
                ->required(),

            TextInput::make('tools.list.sleep-help')
                ->label('Help to sleep')
                ->required(),

            TextInput::make('tools.list.sleep-habits')
                ->label('Good sleep habits')
                ->required(),

            TextInput::make('tools.list.sleep-perspective')
                ->label('Change sleep perspective')
                ->required(),

            TextInput::make('tools.list.worry-time')
                ->label('Worry Time')
                ->required(),

            TextInput::make('tools.list.rid')
                ->label('RID')
                ->required(),

            TextInput::make('tools.list.muscle-relaxation')
                ->label('Muscle Relaxation')
                ->required(),

            TextInput::make('tools.list.deep-breathing')
                ->label('Deep Breathing')
                ->required(),

            TextInput::make('tools.list.body-scan')
                ->label('Body Scan')
                ->required(),

            TextInput::make('tools.list.julia')
                ->label('Julia')
                ->required(),

            TextInput::make('tools.list.robyn')
                ->label('Robyn')
                ->required(),

            TextInput::make('tools.list.forest')
                ->label('Forest')
                ->required(),

            TextInput::make('tools.list.country-road')
                ->label('Country Road')
                ->required(),

            TextInput::make('tools.list.beach')
                ->label('Beach')
                ->required(),

            TextInput::make('tools.list.positive-imagery')
                ->label('Positive Imagery')
                ->required(),

            TextInput::make('tools.list.my-strengths')
                ->label('My Strengths')
                ->required(),

            TextInput::make('tools.list.shift-thoughts')
                ->label('Shift Thoughts')
                ->required(),

            TextInput::make('tools.list.soothe-senses')
                ->label('Soothe The Senses')
                ->required(),

            TextInput::make('tools.list.connect-with-others')
                ->label('Connect With Others')
                ->required(),

            TextInput::make('tools.list.change-perspective')
                ->label('Change Your Perspective')
                ->required(),

            TextInput::make('tools.list.grounding')
                ->label('Grounding')
                ->required(),

            TextInput::make('tools.list.quotes')
                ->label('Inspirational Quotes')
                ->required(),

            TextInput::make('tools.list.recreational-activities')
                ->label('Recreational Activities')
                ->required(),

            TextInput::make('tools.list.recreational-activities-alone')
                ->label('Recreational Activities: Time Spent Alone')
                ->required(),

            TextInput::make('tools.list.recreational-activities-city')
                ->label('Recreational Activities: In The City')
                ->required(),

            TextInput::make('tools.list.recreational-activities-nature')
                ->label('Recreational Activities: In Nature')
                ->required(),
        ];
    }
}
