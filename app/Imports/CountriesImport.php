<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class CountriesImport implements ToModel, WithBatchInserts, WithHeadingRow, WithUpserts
{
    public function model(array $row): ?Country
    {
        return new Country([
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