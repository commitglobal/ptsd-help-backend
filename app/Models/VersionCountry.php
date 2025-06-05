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

    protected $attributes = [
        'tools' => '{}',
    ];

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
