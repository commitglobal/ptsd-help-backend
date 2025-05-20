<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionResource\Actions;

use App\Models\Version;
use Filament\Pages\Actions\Action;

class PublishVersionAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'publish_version';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->color('success');

        $this->action(function (Version $record, Action $action) {
            $record->publish();
            $action->success();
        });

        $this->requiresConfirmation();

        $this->label(__('version.action.change_status.publish.button'));

        $this->modalHeading(__('version.action.change_status.publish.heading'));
        $this->modalSubheading(__('version.action.change_status.publish.subheading'));
        $this->modalButton(__('version.action.change_status.publish.button'));

        $this->successNotificationTitle(__('version.action.change_status.publish.success'));
    }
}
