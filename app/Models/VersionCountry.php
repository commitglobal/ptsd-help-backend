<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersionCountry extends Model
{
    protected $table = 'version_country';

    protected $fillable = [
        'version_id',
        'country_id',
        'tools',
    ];

    protected $casts = [
        'tools' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Set default tools value if not provided
        if (! isset($this->tools)) {
            $this->tools = [
                'relationships' => [
                    'enabled' => true,
                    'relationships-reconnect-with-partner' => true,
                    'relationships-positive-communication' => true,
                    'relationships-i-messages' => true,
                    'relationships-healthy-arguments' => true,
                ],
                'ambient-sounds' => true,
                'mindfulness' => [
                    'enabled' => true,
                    'mindfulness-conscious-breathing' => true,
                    'mindfulness-mindful-walking' => true,
                    'mindfulness-emotional-discomfort' => true,
                    'mindfulness-sense-awareness' => true,
                    'mindfulness-loving-kindness' => true,
                ],
                'pause' => true,
                'my-feelings' => true,
                'worry-time' => true,
                'rid' => true,
                'recreational-activities' => [
                    'enabled' => true,
                    'recreational-activities-alone' => true,
                    'recreational-activities-city' => true,
                    'recreational-activities-nature' => true,
                ],
                'sleep' => [
                    'enabled' => true,
                    'sleep-help' => true,
                    'sleep-habits' => true,
                    'sleep-perspective' => true,
                ],
                'my-strengths' => true,
            ];
        }
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'version_country_language')
            ->using(VersionCountryLanguage::class);
    }

    public function countryLanguages()
    {
        return $this->hasMany(VersionCountryLanguage::class);
    }
}
