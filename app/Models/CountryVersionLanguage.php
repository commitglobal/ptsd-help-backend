<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class CountryVersionLanguage extends Model
{
    protected $table = 'country_version_language';

    public function countryVersion()
    {
        return $this->belongsTo(CountryVersion::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
