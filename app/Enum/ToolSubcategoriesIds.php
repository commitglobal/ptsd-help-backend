<?php

declare(strict_types=1);

namespace App\Enum;

use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;

enum ToolSubcategoriesIds: string
{
    use Arrayable;
    use Comparable;

    case RECONNECT_WITH_PARTNER = 'relationships-reconnect-with-partner';
    case POSITIVE_COMMUNICATION = 'relationships-positive-communication';
    case I_MESSAGES = 'relationships-i-messages';
    case HEALTHY_ARGUMENTS = 'relationships-healthy-arguments';
    case CONSCIOUS_BREATHING = 'mindfulness-conscious-breathing';
    case MINDFUL_WALKING = 'mindfulness-mindful-walking';
    case EMOTIONAL_DISCOMFORT = 'mindfulness-emotional-discomfort';
    case SENSE_AWARENESS = 'mindfulness-sense-awareness';
    case LOVING_KINDNESS = 'mindfulness-loving-kindness';
    case SLEEP_HELP = 'sleep-help';
    case SLEEP_HABITS = 'sleep-habits';
    case SLEEP_PERSPECTIVE = 'sleep-perspective';
    case RECREATIONAL_ACTIVITIES_ALONE = 'recreational-activities-alone';
    case RECREATIONAL_ACTIVITIES_CITY = 'recreational-activities-city';
    case RECREATIONAL_ACTIVITIES_NATURE = 'recreational-activities-nature';
    case CLOUDS = 'clouds';
    case RIVER = 'river';
    case BEACH = 'beach';
    case COUNTRY_ROAD = 'country-road';
    case FOREST = 'forest';
    case JULIA = 'julia';
    case ROBYN = 'robyn';
}
