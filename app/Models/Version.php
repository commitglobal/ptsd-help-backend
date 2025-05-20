<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enum\VersionStatus;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\SoftDeletes;
class Version extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
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

}
