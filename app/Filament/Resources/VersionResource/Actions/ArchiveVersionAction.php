<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Actions;

use App\Models\Version;
use Filament\Pages\Actions\Action;

class ArchiveVersionAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'archive_version';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('warning');

        $this->action(function (Version $record, Action $action) {
            $record->archive();
            $action->success();
        });

        $this->requiresConfirmation();

        $this->label(__('version.action.change_status.archive.button'));

        $this->modalHeading(__('version.action.change_status.archive.heading'));
        $this->modalSubheading(__('version.action.change_status.archive.subheading'));
        $this->modalButton(__('version.action.change_status.archive.button'));

        $this->successNotificationTitle(__('version.action.change_status.archive.success'));
    }
}
