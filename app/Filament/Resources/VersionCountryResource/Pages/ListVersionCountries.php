<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\VersionCountryResource;
use Filament\Resources\Pages\ListRecords;

class ListVersionCountries extends ListRecords
{
    protected static string $resource = VersionCountryResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }
}
