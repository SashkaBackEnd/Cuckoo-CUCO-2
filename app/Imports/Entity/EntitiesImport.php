<?php

namespace App\Imports\Entity;

use App\Entity;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EntitiesImport implements WithMultipleSheets, WithHeadingRow
{


    public function sheets(): array
    {
        return [
            new EntitiesSheet(),
            new PostsSheet(),
            new PostWorktableSheet(),
            new PostWorktableDatesSheet()
        ];
    }
}
