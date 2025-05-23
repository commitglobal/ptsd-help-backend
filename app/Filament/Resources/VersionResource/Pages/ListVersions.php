<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\VersionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVersions extends ListRecords
{
    protected static string $resource = VersionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
