<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CentralPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'createdAt' => strtotime($this->created_at),
            'updatedAt' => strtotime($this->updatedAt),
            'name' => $this->name,
            'phone' => transformPhoneByMask($this->phone),
            'sosStatus' => $this->sos_status,
            'entityId' => $this->entity_id,
            'isCentral' => $this->entity->central_guarded_objects_id === $this->id,
            'standardWork' => WorkTimetableResource::collection($this->workTimetable),
            'nonStandardWork' => WorkTimetableDatesResource::collection($this->workTimetableDates),
            'lastListCheck' => $this->lastListCheck(),
            'currentShifts' => $this->currentShifts(),
            'finishingShift' => $this->finishingShift(),
            'log' => $this->log()
        ];
    }
}
