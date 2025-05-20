<?php

declare(strict_types=1);

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLanguage extends ViewRecord
{
    protected static string $resource = LanguageResource::class;

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
