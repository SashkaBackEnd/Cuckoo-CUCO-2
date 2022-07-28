<?php

namespace App\Exports\Entity;

use App\Entity;
use App\GuardedObject;
use App\WorkTimetableDates;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EntitiesExport implements WithMultipleSheets
{
    use Exportable;

    /**
     * @var Entity
     */
    private $entities;

    /**
     * @var Collection
     */
    private $posts;

    /**
     * @var Collection
     */
    private $workTimetableDates;

    public function __construct(Collection $entities)
    {
        $this->entities = $entities;
        $this->posts = GuardedObject::all();
        $this->workTimetableDates = WorkTimetableDates::query()->with('hours')
            ->orderBy('guarded_objects_id')
            ->orderBy('day')
            ->get();
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new EntitiesSheet($this->entities),
            new PostsSheet($this->posts),
            new PostWorktableSheet($this->posts),
            new PostWorktableDatesSheet($this->workTimetableDates)
        ];
    }
}
