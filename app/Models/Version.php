<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\VersionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class Version extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => VersionStatus::class,
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => VersionStatus::drafted,
    ];

    public function scopeWherePublished(Builder $query): Builder
    {
        return $query->where('status', VersionStatus::published);
    }

    public function archive(): bool
    {
        return $this->update([
            'status' => VersionStatus::archived,
        ]);
    }

    public function publish(): bool
    {
        return $this->update([
            'status' => VersionStatus::published,
            'published_at' => Date::now(),
        ]);
    }

    public function draft(): bool
    {
        return $this->update([
            'status' => VersionStatus::drafted,
        ]);
    }

    public function isArchived(): bool
    {
        return $this->status->is(VersionStatus::archived);
    }

    public function isPublished(): bool
    {
        return $this->status->is(VersionStatus::published);
    }

    public function isDrafted(): bool
    {
        return $this->status->is(VersionStatus::drafted);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'version_country');
    }

    public function versionCountries()
    {
        return $this->hasMany(related: VersionCountry::class);
    }
}
