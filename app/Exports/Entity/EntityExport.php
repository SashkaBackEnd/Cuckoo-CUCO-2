<?php

namespace App\Exports\Entity;

use App\Entity;
use App\WorkTimetableDates;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EntityExport implements WithMultipleSheets
{
    use Exportable;

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var Collection
     */
    private $posts;

    /**
     * @var Collection
     */
    private $workTimetableDates;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
        $this->posts = $entity->posts;
        $this->workTimetableDates = WorkTimetableDates::query()->with('hours')
            ->whereIn('guarded_objects_id', $this->posts->pluck('id'))
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
            new EntitySheet($this->entity),
            new PostSheet($this->posts),
            new PostWorktableSheet($this->posts),
            new PostWorktableDatesSheet($this->workTimetableDates)
        ];
    }
}
