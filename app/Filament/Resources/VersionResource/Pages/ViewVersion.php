<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\VersionResource;
use App\Filament\Resources\VersionResource\Actions\ArchiveVersionAction;
use App\Filament\Resources\VersionResource\Actions\DraftVersionAction;
use App\Filament\Resources\VersionResource\Actions\PublishVersionAction;
use App\Models\Version;
use Filament\Pages\Actions\DeleteAction;
use Filament\Pages\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVersion extends ViewRecord
{
    protected static string $resource = VersionResource::class;

    public function getTitle(): string
    {
        return $this->getRecord()->name;
    }

    protected function getActions(): array
    {
        return [
            PublishVersionAction::make()
                ->hidden(fn(Version $record) => $record->isPublished())
                ->record($this->getRecord()),

            DraftVersionAction::make()
                ->hidden(fn(Version $record) => $record->isDrafted())
                ->record($this->getRecord()),

            ArchiveVersionAction::make()
                ->hidden(fn(Version $record) => $record->isArchived())
                ->record($this->getRecord()),

            EditAction::make(),

            DeleteAction::make(),
        ];
    }
}
