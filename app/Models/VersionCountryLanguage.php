<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionCountryLanguage extends Model
{
    protected $table = 'version_country_language';

    protected $fillable = [
        'version_id',
        'country_id',
        'language_id',
        'tools',
        'symptoms',
        'support',
    ];

    protected $casts = [
        'tools' => 'array',
        'symptoms' => 'array',
        'support' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set default tools value if not provided
        if (!isset($this->tools)) {
            $this->tools = [
                'relationships' => [
                    'label' => 'relationships',
                    'enabled' => true,
                    'subcategories' => [
                        'relationships-reconnect-with-partner' => [
                            'label' => 'relationships-reconnect-with-partner',
                            'enabled' => true,
                        ],
                        'relationships-positive-communication' => [
                            'label' => 'relationships-positive-communication',
                            'enabled' => true,
                        ],
                        'relationships-i-messages' => [
                            'label' => 'relationships-i-messages',
                            'enabled' => true,
                        ],
                        'relationships-healthy-arguments' => [
                            'label' => 'relationships-healthy-arguments',
                            'enabled' => true,
                        ],
                    ],
                ],
                'ambient-sounds' => ['label' => 'ambient-sounds', 'enabled' => true],
                'mindfulness' => [
                    'label' => 'mindfulness',
                    'enabled' => true,
                    'subcategories' => [
                        'mindfulness-conscious-breathing' => [
                            'label' => 'mindfulness-conscious-breathing',
                            'enabled' => true,
                        ],
                        'mindfulness-mindful-walking' => [
                            'label' => 'mindfulness-mindful-walking',
                            'enabled' => true,
                        ],
                        'mindfulness-emotional-discomfort' => [
                            'label' => 'mindfulness-emotional-discomfort',
                            'enabled' => true,
                        ],
                        'mindfulness-sense-awareness' => [
                            'label' => 'mindfulness-sense-awareness',
                            'enabled' => true,
                        ],
                        'mindfulness-loving-kindness' => [
                            'label' => 'mindfulness-loving-kindness',
                            'enabled' => true,
                        ],
                    ],
                ],
                'pause' => ['label' => 'pause', 'enabled' => true],
                'my-feelings' => ['label' => 'my-feelings', 'enabled' => true],
                'worry-time' => ['label' => 'worry-time', 'enabled' => true],
                'rid' => ['label' => 'rid', 'enabled' => true],
                'recreational-activities' => [
                    'label' => 'recreational-activities',
                    'enabled' => true,
                    'subcategories' => [
                        'recreational-activities-alone' => [
                            'label' => 'recreational-activities-alone',
                            'enabled' => true,
                        ],
                        'recreational-activities-city' => [
                            'label' => 'recreational-activities-city',
                            'enabled' => true,
                        ],
                        'recreational-activities-nature' => [
                            'label' => 'recreational-activities-nature',
                            'enabled' => true,
                        ],
                    ],
                ],
                'sleep' => [
                    'label' => 'sleep',
                    'enabled' => true,
                    'subcategories' => [
                        'sleep-help' => [
                            'label' => 'sleep-help',
                            'enabled' => true,
                        ],
                        'sleep-habits' => [
                            'label' => 'sleep-habits',
                            'enabled' => true,
                        ],
                        'sleep-perspective' => [
                            'label' => 'sleep-perspective',
                            'enabled' => true,
                        ],
                    ],
                ],
                'my-strengths' => ['label' => 'my-strengths', 'enabled' => true],
            ];
        }
    }

    public function version()
    {
        return $this->belongsTo(related: Version::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
