<?php

namespace App\Enum;
use App\Concerns\Enums\Arrayable;
use App\Concerns\Enums\Comparable;
use App\Concerns\Enums\HasLabel;
enum VersionStatus: string
{
    use Arrayable;
    use Comparable;
    use HasLabel;

    case published = 'published';
    case archived = 'archived';
    case drafted = 'drafted';

    protected function labelKeyPrefix(): ?string
    {
        return 'version.status';
    }
}
