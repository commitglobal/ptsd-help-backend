<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
    ];
}
