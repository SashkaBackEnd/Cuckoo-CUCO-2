<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * @var bool
     */
    private static $addIsCentral = false;
    private static $workTimetableRelation = false;
    private static $workTimetableDateRelation = false;
    private static $addFullInfo = false;

    /**
     * @param bool $add
     */
    public static function addIsCentral(bool $add)
    {
        self::$addIsCentral = $add;
    }

    public static function workTimetableRelation(bool $set)
    {
        self::$workTimetableRelation = $set;
    }

    public static function workTimetableDateRelation(bool $set)
    {
        self::$workTimetableDateRelation = $set;
    }

    public static function addFullInfo(bool $add)
    {
        self::$addFullInfo = $add;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'createdAt' => strtotime($this->created_at),
            'updatedAt' => strtotime($this->updatedAt),
            'name' => $this->name,
            'phone' => transformPhoneByMask($this->phone),
            'sosStatus' => $this->sos_status,
            'entityId' => $this->entity_id,
            'lastListCheck' => self::$addFullInfo ? $this->lastListCheck() : null,
            'currentShifts' => $this->currentShifts() ?? null,
            'finishingShift' => self::$addFullInfo ? $this->finishingShift() : null,
            'log' => self::$addFullInfo ? $this->log() : null,
        ];

        if (self::$addIsCentral) {
            $isCentral = $this->entity->central_guarded_objects_id === $this->id;
            $data['isCentral'] = $isCentral;

            if ($isCentral) {
                $data['lastListCheck'] = $this->lastListCheck();
                $data['finishingShift'] = $this->finishingShift();
                $data['log'] = $this->log();
            }
        }

        if (self::$workTimetableRelation) {
            $data['standardWork'] = WorkTimetableResource::collection($this->workTimetable);
        }

        if (self::$workTimetableDateRelation) {
            $data['nonStandardWork'] = WorkTimetableDatesResource::collection($this->workTimetableDates);
        }

        return $data;
    }
}
