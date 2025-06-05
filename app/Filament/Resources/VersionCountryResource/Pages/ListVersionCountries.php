<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Pages;
use Filament\Pages\Actions;

use App\Filament\Resources\VersionCountryResource;
use Filament\Resources\Pages\ListRecords;

class ListVersionCountries extends ListRecords
{
    protected static string $resource = VersionCountryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),

        ];
    }
}
