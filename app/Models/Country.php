<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
    ];

    public function versionCountries()
    {
        return $this->belongsToMany(Version::class, 'version_country', 'country_id', 'version_id')->withTimestamps();
    }
}
