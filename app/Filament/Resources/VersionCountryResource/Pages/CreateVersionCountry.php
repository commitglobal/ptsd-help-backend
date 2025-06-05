<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Pages;

use App\Filament\Resources\VersionCountryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVersionCountry extends CreateRecord
{
    protected static string $resource = VersionCountryResource::class;

    protected static bool $canCreateAnother = false;
}
