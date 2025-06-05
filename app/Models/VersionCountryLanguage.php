<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionCountryLanguage extends Model
{
    protected $table = 'version_country_language';

    protected $fillable = [
        'version_country_id',
        'language_id',
        'symptoms',
        'support',
    ];

    protected $casts = [
        'symptoms' => 'array',
        'support' => 'array',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function versionCountry()
    {
        return $this->belongsTo(VersionCountry::class, 'version_country_id');
    }
}
