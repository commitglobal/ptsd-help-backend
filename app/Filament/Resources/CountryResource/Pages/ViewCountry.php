<?php

declare(strict_types=1);

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCountry extends ViewRecord
{
    protected static string $resource = CountryResource::class;

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
