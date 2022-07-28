<?php

namespace App\Imports\Entity;

use App\Entity;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EntityImport implements WithMultipleSheets, WithHeadingRow
{
    /**
     * @var Entity
     */
    private $entity;

    /**
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function sheets(): array
    {
        return [
            new EntitySheet($this->entity),
            new PostSheet($this->entity),
            new PostWorktableSheet(),
            new PostWorktableDatesSheet()
        ];
    }
}
