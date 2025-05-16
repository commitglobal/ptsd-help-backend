<?php

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\VersionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVersion extends EditRecord
{
    protected static string $resource = VersionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
