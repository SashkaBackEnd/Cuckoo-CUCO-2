<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkTimetableResource extends JsonResource
{
    /**
     * Отображение отношения центрального поста
     * @var bool
     */
    private static $hours = true;

    /**
     * @param bool $set
     */
    public static function setHoursRelationships(bool $set)
    {
        self::$hours = $set;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'day' => $this->day,
            'salary' => $this->salary,
            'createdAt' => strtotime($this->created_at),
            'updatedAt' => strtotime($this->updated_at),
        ];

        if (self::$hours) {
            $data['hours'] = WorkHoursResource::collection($this->hours);
        }
        return $data;
    }
}
