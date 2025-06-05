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
        // Eager load the relationships if not already loaded
        $this->getRecord()->loadMissing(['version', 'country']);

        return sprintf(
            '%s - %s',
            $this->getRecord()->version->name,
            $this->getRecord()->country->name
        );
    }

    protected function getActions(): array
    {
        return [
            EditAction::make(),

            DeleteAction::make(),
        ];
    }
}
