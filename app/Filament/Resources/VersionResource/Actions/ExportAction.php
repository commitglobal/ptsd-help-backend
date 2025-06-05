<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Actions;

use Filament\Tables\Actions\Action;

class ExportAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make('export-files')
            ->label(__('Export'))
            ->color('primary')
            ->action(function ($record) {
                dd($record);
            });
    }
}
