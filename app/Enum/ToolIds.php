<?php

declare(strict_types=1);

namespace App\Enum;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;

enum ToolIds: string
{
    use Arrayable;
    use Comparable;

    case RELATIONSHIPS = 'relationships';
    case AMBIENT_SOUNDS = 'ambient-sounds';
    case MINDFULNESS = 'mindfulness';
    case PAUSE = 'pause';
    case MY_FEELINGS = 'my-feelings';
    case SLEEP = 'sleep';
    case WORRY_TIME = 'worry-time';
    case RID = 'rid';
    case SOOTHE_SENSES = 'soothe-senses';
    case CONNECT_WITH_OTHERS = 'connect-with-others';
    case CHANGE_PERSPECTIVE = 'change-perspective';
    case GROUNDING = 'grounding';
    case QUOTES = 'quotes';
    case RECREATIONAL_ACTIVITIES = 'recreational-activities';
    case MY_STRENGTHS = 'my-strengths';
    case SHIFT_THOUGHTS = 'shift-thoughts';
    case OBSERVE_THOUGHTS = 'observe-thoughts';
    case POSTIVE_IMAGERY = 'postive-imagery';
    case BODY_SCAN = 'body-scan';
    case MUSCLE_RELAXATION = 'muscle-relaxation';
    case DEEP_BREATHING = 'deep-breathing';
}
