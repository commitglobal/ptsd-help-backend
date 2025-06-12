<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Pages;

use App\Filament\Resources\VersionCountryLanguageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVersionCountryLanguage extends EditRecord
{
    protected static string $resource = VersionCountryLanguageResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        $versionName = $record->versionCountry->version->name ?? 'Unknown Version';
        $countryName = $record->versionCountry->country->name ?? 'Unknown Country';
        $languageName = $record->language->name ?? 'Unknown Language';

        return "{$versionName} / {$countryName} / {$languageName}";
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
