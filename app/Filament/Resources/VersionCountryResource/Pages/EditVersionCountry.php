<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Pages;

use App\Filament\Resources\VersionCountryResource;
use Filament\Resources\Pages\EditRecord;

class EditVersionCountry extends EditRecord
{
    protected static string $resource = VersionCountryResource::class;

    protected function getActions(): array
    {
        return [
            //
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
