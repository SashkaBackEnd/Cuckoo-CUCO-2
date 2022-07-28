<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\EntityManager;
use App\Entity;
use App\Event;
use App\GuardedObject;
use App\WorkShift;
use Carbon\Carbon;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $entities = $this->entities;
        $temp = [];
        foreach ($entities as $value) {
            $temp[] = $value->id;
        } 
        $status = 1;
        if ($this->block == 1) {
            $status = 0;
        }
        if (count($temp) != 0)  {
            $status = 2;
        }
        $guarded_objects_count = GuardedObject::whereIn('entity_id', $temp)->get()->count();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'patronymic' => $this->patronymic,
            'phone' => $this->phone,
            'roleType' => $this->role_type,
            'email' => $this->email,
            'log' => [],
            'entities' => $this->entities,
            'access' => $this->access,
            'guarded_objects_count' => $guarded_objects_count,
            'worker_count' => 0,
            'status' => $status
        ];
    }
}
