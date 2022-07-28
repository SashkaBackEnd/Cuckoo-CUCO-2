<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\EntityManager;
use App\Entity;
use App\Event;
use App\GuardedObject;
use App\WorkShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class UserResource extends JsonResource
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
        $guarded_objects = GuardedObject::whereIn('entity_id', $temp)->get();

       	$entities_arr = [];
       	foreach ($entities as $item) {
       		$entities_arr[$item->id]['entity'] = $item;
       		$temp = [];
	        foreach ($guarded_objects as  $guard) {
	        	if ($guard->entity_id == $item->id) {
	        		$temp[] = $guard->id;
	        	}
	        }
       		$entities_arr[$item->id]['guarded_objects_count'] = count($temp);

       		// Кол-во работников
       		$entities_arr[$item->id]['worker_count'] = $countPostsWithoutGuards = DB::table('guarded_objects')
                ->whereIn('id', $temp)
                ->selectRaw('COUNT(id) count_id')
                ->whereRaw(DB::raw("id IN (SELECT guarded_object_id FROM work_shifts WHERE shift_status = 'process')"))
                ->whereNull('deleted_at')
                ->value('count_id');
       	}

        $temp = [];
        foreach ($guarded_objects as  $item) {
            $temp[] = $item->id;
        }
        $guarded_objects_count = count($temp);

        $events = Event::whereIn('guarded_object_id', $temp)->where('created_at', '>', Carbon::now()->subHours(24))->orderBy('created_at', 'desc')->get()->map->listInfo();

      

        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'patronymic' => $this->patronymic,
            'phone' => $this->phone,
            'roleType' => $this->role_type,
            'email' => $this->email,
            'log' => $events,
            'entities' => $entities_arr,
            'access' => $this->access,
            'status' => $status
        ];
    }
}
