<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\WorkShift;
use App\GuardedObject;
use App\QueuedCall;
use App\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EntityResource extends JsonResource
{
    /**
     * Отображение отношения центрального поста
     * @var bool
     */
    private static $centralPostRelation = false;

    /**
     * Отображение отношения всех постов
     * @var bool
     */
    private static $posts = false;

    /**
     * Отображение отношения всех постов
     * @var bool
     */
    private static $customers = false;

    /**
     * @param bool $set
     */
    public static function setCentralPostRelationships(bool $set)
    {
        self::$centralPostRelation = $set;
    }

    /**
     * @param bool $set
     */
    public static function setPostsRelationships(bool $set)
    {
        self::$posts = $set;
    }

    public static function setCustomersRelationships(bool $set)
    {
        self::$customers = $set;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {

        // Подсчет работников через смены
        $guarded_objects = $this->posts;
        $temp = [];
        foreach ($guarded_objects as  $item) {
            $temp[] = $item->id;
        }
        
        $workers_count = $countPostsWithoutGuards = DB::table('guarded_objects')
            ->where('entity_id', $this->id)
            ->selectRaw('COUNT(id) count_id')
            ->whereRaw(DB::raw("id IN (SELECT guarded_object_id FROM work_shifts WHERE shift_status = 'process')"))
            ->whereNull('deleted_at')
            ->value('count_id');

        $last_check = 'Нет данных';

        // Последняя проверка
        $event = Event::whereIn('guarded_object_id', $temp)->orderBy('created_at', 'desc')->first();
        if ($event != null) {
            $last_check = GuardedObject::where('id', $event->guarded_object_id)->first()->lastListCheck();
        }


        // События за последние 24 часа

        $events = Event::whereIn('guarded_object_id', $temp)->where('created_at', '>', Carbon::now()->subHours(300))->get();

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'managers_count' => $this->managers_count,
            'workers_count' => $workers_count,
            'last_check' => $last_check,
            'events' => $events,
            'phone' => transformPhoneByMask($this->phone),
            'comment' => $this->comment,
            'address' => $this->address,
            'servicePhone' => transformPhoneByMask($this->service_phone),
            'customerName' => $this->customer_name,
            'quantityCalls' => $this->quantity_calls,
            'callTo' => strtotime($this->call_to),
            'callFrom' => strtotime($this->call_from),
            'createdAt' => strtotime($this->created_at),
            'updatedAt' => strtotime($this->updated_at),
            'dialingStatus' => $this->dialing_status,
            'maxDurationWork' => $this->max_duration_work,
            'customerContacts' => $this->customer_contacts,
            'callBackQuantity' => $this->call_back_quantity,
            'centralPost' => self::$centralPostRelation ? new CentralPostResource($this->centralPost) : $this->central_guarded_objects_id
        ];

        if (self::$posts) {
            $data['posts'] = PostResource::collection($this->posts); // todo добавить ресурс
        }

        if (self::$customers) {
            $data['customers'] = EntityCustomerResource::collection($this->customers);
        }

        return $data;
    }
}
