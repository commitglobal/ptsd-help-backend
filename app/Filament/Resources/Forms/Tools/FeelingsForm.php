<?php

declare(strict_types=1);

namespace App\Filament\Resources\Forms\Tools;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;

class FeelingsForm
{
    public static function getContentSchema(): array
    {
        return [
            Group::make()
                ->label('Angry')
                ->schema([
                    // Angry
                    TextInput::make('tools.feelings.angry.main')->label('Angry')->required(),
                    TextInput::make('tools.feelings.angry.betrayed')->label('Betrayed')->required(),
                    TextInput::make('tools.feelings.angry.bitter')->label('Bitter')->required(),
                    TextInput::make('tools.feelings.angry.critical')->label('Critical')->required(),
                    TextInput::make('tools.feelings.angry.devastated')->label('Devastated')->required(),
                    TextInput::make('tools.feelings.angry.disgusted')->label('Disgusted')->required(),
                    TextInput::make('tools.feelings.angry.frustrated')->label('Frustrated')->required(),
                    TextInput::make('tools.feelings.angry.detestable')->label('Detestable')->required(),
                    TextInput::make('tools.feelings.angry.hostile')->label('Hostile')->required(),
                    TextInput::make('tools.feelings.angry.hurt')->label('Hurt')->required(),
                    TextInput::make('tools.feelings.angry.irritated')->label('Irritated')->required(),
                    TextInput::make('tools.feelings.angry.indignant')->label('Indignant')->required(),
                ]),

            Group::make()
                ->label('Happy')
                ->schema([
                    // Happy
                    TextInput::make('tools.feelings.happy.main')->label('Happy')->required(),
                    TextInput::make('tools.feelings.happy.energetic')->label('Energetic')->required(),
                    TextInput::make('tools.feelings.happy.cheerful')->label('Cheerful')->required(),
                    TextInput::make('tools.feelings.happy.creative')->label('Creative')->required(),
                    TextInput::make('tools.feelings.happy.curious')->label('Curious')->required(),
                    TextInput::make('tools.feelings.happy.daring')->label('Daring')->required(),
                    TextInput::make('tools.feelings.happy.hopeful')->label('Hopeful')->required(),
                    TextInput::make('tools.feelings.happy.full_of_imagination')->label('Full of imagination')->required(),
                    TextInput::make('tools.feelings.happy.light')->label('Light')->required(),
                    TextInput::make('tools.feelings.happy.optimistic')->label('Optimistic')->required(),
                    TextInput::make('tools.feelings.happy.playful')->label('Playful')->required(),
                    TextInput::make('tools.feelings.happy.thin_skinned')->label('Thin-skinned')->required(),
                    TextInput::make('tools.feelings.happy.provocative')->label('Provocative')->required(),
                ]),

            Group::make()
                ->label('Strong')
                ->schema([
                    // Strong
                    TextInput::make('tools.feelings.strong.main')->label('Strong')->required(),
                    TextInput::make('tools.feelings.strong.appreciated')->label('Appreciated')->required(),
                    TextInput::make('tools.feelings.strong.confident')->label('Confident')->required(),
                    TextInput::make('tools.feelings.strong.discerning')->label('Discerning')->required(),
                    TextInput::make('tools.feelings.strong.energetic')->label('Energetic')->required(),
                    TextInput::make('tools.feelings.strong.nourishing')->label('Nourishing')->required(),
                    TextInput::make('tools.feelings.strong.proud')->label('Proud')->required(),
                    TextInput::make('tools.feelings.strong.respected')->label('Respected')->required(),
                    TextInput::make('tools.feelings.strong.receptive')->label('Receptive')->required(),
                    TextInput::make('tools.feelings.strong.successful')->label('Successful')->required(),
                    TextInput::make('tools.feelings.strong.grateful')->label('Grateful')->required(),
                    TextInput::make('tools.feelings.strong.cherished')->label('Cherished')->required(),
                    TextInput::make('tools.feelings.strong.valuable')->label('Valuable')->required(),
                ]),

            Group::make()
                ->label('Sad')
                ->schema([
                    // Sad
                    TextInput::make('tools.feelings.sad.main')->label('Sad')->required(),
                    TextInput::make('tools.feelings.sad.embarrassed')->label('Embarrassed')->required(),
                    TextInput::make('tools.feelings.sad.bored')->label('Bored')->required(),
                    TextInput::make('tools.feelings.sad.cold')->label('Cold')->required(),
                    TextInput::make('tools.feelings.sad.depressed')->label('Depressed')->required(),
                    TextInput::make('tools.feelings.sad.desperate')->label('Desperate')->required(),
                    TextInput::make('tools.feelings.sad.disconnected')->label('Disconnected')->required(),
                    TextInput::make('tools.feelings.sad.empty')->label('Empty')->required(),
                    TextInput::make('tools.feelings.sad.hopeless')->label('Hopeless')->required(),
                    TextInput::make('tools.feelings.sad.humiliated')->label('Humiliated')->required(),
                    TextInput::make('tools.feelings.sad.lonely')->label('Lonely')->required(),
                    TextInput::make('tools.feelings.sad.tired')->label('Tired')->required(),
                    TextInput::make('tools.feelings.sad.worthless')->label('Worthless')->required(),
                ]),

            Group::make()
                ->label('Safe')
                ->schema([
                    // Safe
                    TextInput::make('tools.feelings.safe.main')->label('Safe')->required(),
                    TextInput::make('tools.feelings.safe.accepted')->label('Accepted')->required(),
                    TextInput::make('tools.feelings.safe.calm')->label('Calm')->required(),
                    TextInput::make('tools.feelings.safe.loved')->label('Loved')->required(),
                    TextInput::make('tools.feelings.safe.open')->label('Open')->required(),
                    TextInput::make('tools.feelings.safe.peaceful')->label('Peaceful')->required(),
                    TextInput::make('tools.feelings.safe.protected')->label('Protected')->required(),
                    TextInput::make('tools.feelings.safe.silent')->label('Silent')->required(),
                    TextInput::make('tools.feelings.safe.relaxed')->label('Relaxed')->required(),
                    TextInput::make('tools.feelings.safe.caring')->label('Caring')->required(),
                    TextInput::make('tools.feelings.safe.understood')->label('Understood')->required(),
                    TextInput::make('tools.feelings.safe.warm')->label('Warm')->required(),
                ]),

            Group::make()
                ->label('Scared')
                ->schema([
                    // Scared
                    TextInput::make('tools.feelings.scared.main')->label('Scared')->required(),
                    TextInput::make('tools.feelings.scared.abandoned')->label('Abandoned')->required(),
                    TextInput::make('tools.feelings.scared.anxious')->label('Anxious')->required(),
                    TextInput::make('tools.feelings.scared.perplexed')->label('Perplexed')->required(),
                    TextInput::make('tools.feelings.scared.helpless')->label('Helpless')->required(),
                    TextInput::make('tools.feelings.scared.inadequate')->label('Inadequate')->required(),
                    TextInput::make('tools.feelings.scared.insignificant')->label('Insignificant')->required(),
                    TextInput::make('tools.feelings.scared.numb')->label('Numb')->required(),
                    TextInput::make('tools.feelings.scared.overwhelmed')->label('Overwhelmed')->required(),
                    TextInput::make('tools.feelings.scared.paralyzed')->label('Paralyzed')->required(),
                    TextInput::make('tools.feelings.scared.shocked')->label('Shocked')->required(),
                    TextInput::make('tools.feelings.scared.blocked')->label('Blocked')->required(),
                    TextInput::make('tools.feelings.scared.vulnerable')->label('Vulnerable')->required(),
                ]),
        ];
    }
}
