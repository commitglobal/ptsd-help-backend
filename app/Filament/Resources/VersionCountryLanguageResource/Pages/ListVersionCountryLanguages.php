<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Pages;

use App\Filament\Resources\VersionCountryLanguageResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVersionCountryLanguages extends ListRecords
{
    protected static string $resource = VersionCountryLanguageResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
