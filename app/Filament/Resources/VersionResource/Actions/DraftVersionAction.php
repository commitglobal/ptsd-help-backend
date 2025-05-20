<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Actions;

use App\Models\Version;
use Filament\Pages\Actions\Action;

class DraftVersionAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'draft_version';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('secondary');

        $this->action(function (Version $record, Action $action) {
            $record->draft();
            $action->success();
        });

        $this->requiresConfirmation();

        $this->label(__('version.action.change_status.draft.button'));

        $this->modalHeading(__('version.action.change_status.draft.heading'));
        $this->modalSubheading(__('version.action.change_status.draft.subheading'));
        $this->modalButton(__('version.action.change_status.draft.button'));

        $this->successNotificationTitle(__('version.action.change_status.draft.success'));
    }
}
