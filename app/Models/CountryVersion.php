<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CountryVersion extends Model
{
    protected $table = 'country_version';

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}