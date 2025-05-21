<?php

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

    public function versions()
    {
        return $this->belongsToMany(Version::class, 'country_version', 'country_id', 'version_id')->withTimestamps();
    }
}
