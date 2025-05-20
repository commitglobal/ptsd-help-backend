<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Language;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class LanguagesImport implements ToModel, WithBatchInserts, WithHeadingRow, WithUpserts
{
    public function model(array $row): ?Language
    {
        return new Language([
            'id' => $row['id'],
            'name' => $row['name'],
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }


    public function uniqueBy()
    {
        return 'id';
    }
}