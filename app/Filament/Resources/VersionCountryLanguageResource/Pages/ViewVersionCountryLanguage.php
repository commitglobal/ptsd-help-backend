<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Pages;

use App\Filament\Resources\VersionCountryLanguageResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVersionCountryLanguage extends ViewRecord
{
    protected static string $resource = VersionCountryLanguageResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();

        $versionName = $record->countryVersion->version->name ?? 'Unknown Version';
        $countryName = $record->countryVersion->country->name ?? 'Unknown Country';
        $languageName = $record->language->name ?? 'Unknown Language';

        return "{$versionName} / {$countryName} / {$languageName}";
    }

    protected function getActions(): array
    {
        return [
            EditAction::make(),

            DeleteAction::make(),
        ];
    }
}
