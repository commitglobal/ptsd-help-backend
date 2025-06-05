<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Pages;

use App\Filament\Resources\VersionCountryLanguageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\EditRecord;

class EditVersionCountryLanguage extends EditRecord
{
    protected static string $resource = VersionCountryLanguageResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
