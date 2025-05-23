<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Pages;

use App\Filament\Resources\VersionCountryLanguageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVersionCountryLanguage extends CreateRecord
{
    protected static string $resource = VersionCountryLanguageResource::class;

    protected static bool $canCreateAnother = false;
}
