<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryResource\Pages;

use App\Filament\Resources\VersionCountryResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVersionCountry extends ViewRecord
{
    protected static string $resource = VersionCountryResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            EditAction::make(),

            DeleteAction::make(),
        ];
    }
}
