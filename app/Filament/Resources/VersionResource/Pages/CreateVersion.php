<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\VersionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVersion extends CreateRecord
{
    protected static string $resource = VersionResource::class;

    protected static bool $canCreateAnother = false;


}
