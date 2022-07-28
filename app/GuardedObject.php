<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

// TODO посты
class GuardedObject extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'phone',
        'queue_end_time', 'sos_status', 'number_of_call_attempts', 'no_guard_notification',
        'no_guard_from_time', 'entity_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'deleted_at', 'created_at'
    ];

    protected $appends = ['log', 'lastCheck', 'currentShift'];
    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @return HasOne
     */
    public function entity(): HasOne
    {
        return $this->hasOne(Entity::class, 'id', 'entity_id');
    }

    /**
     * Получение графика работы
     * @return HasMany
     */
    public function workTimetable(): HasMany
    {
        return $this->hasMany(WorkTimetable::class, 'guarded_objects_id', 'id');
    }

    /**
     * Получение нестандартного графика работы
     * @return HasMany
     */
    public function workTimetableDates(): HasMany
    {
        return $this->hasMany(WorkTimetableDates::class, 'guarded_objects_id', 'id')
            ->orderBy('day');
    }

    public function getLogAttribute()
    {
        return [];
    }

    public function getLastCheckAttribute()
    {
        return [];
    }

    public function getCurrentShiftAttribute()
    {
        return [];
    }

    public function events()
    {
        return $this->hasMany('App\Event');
    }

    public function infoForEvent()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => transformPhoneByMask($this->phone)
        ];
    }

    public function listInfo()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "phone" => $this->phone,
            "shortNameHOS" => $this->ShortNameHOS,
            "lastCheck" => $this->lastListCheck(),
            "currentShifts" => $this->currentShifts(),
        ];
    }

    public function fullInfo()
    {
        $finishingShift = $this->workShift()->whereIn('shift_status', ['finishing', 'finishingTimeout'])->first();
        $fullInfo = [
            "id" => $this->id,
            "name" => $this->name,
            "phone" => $this->phone,
            "lastCheck" => $this->lastListCheck(),
            "currentShifts" => $this->currentShifts(),
            "finishingShift" => null,
            "log" => $this->event()->where('created_at', '>', Carbon::now()->subDay()->toDateTimeString())->get()->map->listInfo(),
        ];
        if (!is_null($finishingShift)) {
            $fullInfo['finishingShift'] = $finishingShift->guardedObjectInfo();
        }
        return $fullInfo;
    }

    public function currentShifts()
    {
        $currentShift = $this->workShift()->where('shift_status', 'process')->get();
        if ($currentShift->count() > 0) {
            return $currentShift->map(function ($shift) {
                return $shift->guardedObjectInfo();
            });
        } else {
            return [];
        }
    }

    // todo сделать по человечески
    public function finishingShift()
    {
        $finishingShift = $this->workShift()->whereIn('shift_status', ['finishing', 'finishingTimeout'])->first();
        return !is_null($finishingShift) ? $finishingShift->guardedObjectInfo() : null;
    }

    public function log()
    {
        return $this->event()
            ->where('created_at', '>', Carbon::now()->subDay()->toDateTimeString())
            ->orderByDesc('created_at')
            ->get()->map->listInfo();
    }

    public function lastListCheck(): ?array
    {
        $lastCheck = $this->asterCall()
            ->whereNotNull('process_status')->where('process_status', '!=', 'objectGuardMismatch')
            ->orderBy('call_date', 'desc')
            ->first();

        if (!$lastCheck) {
            return null;
        }
        if ($lastCheck->process_status == 'sos') {
            if ($this->sos_status == 1) {
                $lastCheck = [
                    'date' => Carbon::parse($lastCheck->call_date)->timestamp,
                    'type' => 'sos',
                ];
            } else {
                $lastCheck = null;
            }
        } elseif ($lastCheck->process_status == 'done') {
            $lastCheck = [
                'date' => Carbon::parse($lastCheck->call_date)->timestamp,
                'type' => 'good',
            ];
        } else {
            $lastCheck = [
                'date' => Carbon::parse($lastCheck->call_date)->timestamp,
                'type' => 'bad',
            ];
        }
        return $lastCheck;
    }

    public function Event()
    {
        return $this->hasMany('App\Event');
    }

    public function workShift()
    {
        return $this->hasMany('App\WorkShift');
    }

    public function queuedCalls()
    {
        return $this->hasMany('App\QueuedCall');
    }

    /**
     * @param Carbon $callTo
     * @param Carbon $callFrom
     * @param int $quantityCalls
     */
    public function generateDialQueue(Carbon $callFrom, Carbon $callTo, int $quantityCalls): void
    {
        $workDaySeconds = $callFrom->diffInSeconds($callTo);
        if ($callFrom > $callTo) {
            $callTo->addDay();
            $workDaySeconds = $callTo->diffInSeconds($callFrom);   
        }
        if ($quantityCalls === 0 || $this->queue_end_time - time() >= $workDaySeconds) {
            return;
        }

        $period = $workDaySeconds / $quantityCalls;
        $data = [];
        for ($i = 1; $i <= $quantityCalls; $i++) {
            $time = $period * $i + rand(-$period / 3, $period / 3);
            if (time() < $callFrom->timestamp + $time) {
            	$data[] = [
	                'call_date' => $callFrom->timestamp + $time,
	                'guarded_object_id' => $this->id,
	                'call_status' => 'queued',
	                'created_at' => now(),
	                'updated_at' => now()
            	];
            }
        }


        // Фикс со сменами, нужно тестировать
        if (count(GuardedObject::where('id', $this->id)->first()->currentShifts()) !== 0) {
            QueuedCall::insert($data);
            $queue_end_time = $callTo->addDay()->timestamp;
            $this->queue_end_time = $queue_end_time;     
      
            $this->save();
        }
    }

    public function clearDialQueue()
    {
        $this->queuedCalls()->where('call_status', 'queued')->delete();
        $this->queue_end_time = time();
        $this->save();
    }

    public static function checkObjectsWithoutGuards()
    {
        $noGuardTime = DB::table('global_settings')->where('name', 'shift_change_time')->value('value');
        $objectsToNotify = GuardedObject::where('no_guard_notification', 0)->whereNotNull('no_guard_from_time')->where('no_guard_from_time', '<=', time() - $noGuardTime * 60)->get();
        foreach ($objectsToNotify as $object) {
            $object->no_guard_notification = 1;
            $object->save();
            $eventData = [
                'type' => 'noGuardTimeExceeded',
                'guarded_object_id' => $object->id,
                'security_guard_id' => 0,
            ];
            Event::create($eventData);

            $smsEventData = [
                'type' => 'smsForHeadOfSecurity',
                'guarded_object_id' => $object->id,
                'security_guard_id' => 0,
            ];
            //TODO переделать во втором спринте на менеджеров
//            $phoneHOS = trim($object->phone_hos, '+');
//            $text = $object->name . ' (' . $object->phone . '): пост без охраны более ' . $noGuardTime . ' минут.';
//
//            $sms = new SMS();
//            $smsStatus = $sms->send($phoneHOS, $text);
//
//            Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//            if ($smsStatus->status === "OK") {
//                Event::create($smsEventData);
//            }

            $socketMessage = [
                'fetchObjects' => '',
                'fetchObjectById' => $object->id,
            ];
            /*$rabbit = new Rabbit();
            $rabbit->sendForSocket(json_encode($socketMessage));*/
        }
    }

    public function noteIfNoGuard()
    {
        $currentShifts = $this->workShift()->where('shift_status', 'process')->get();
        if ($currentShifts->count() == 0) {
            if (is_null($this->no_guard_from_time)) {
                $this->no_guard_from_time = time();
                $this->no_guard_notification = 0;
                $this->save();
            }
        }
    }

    public function noteIfHasGuard()
    {
        $currentShifts = $this->workShift()->where('shift_status', 'process')->get();
        if ($currentShifts->count() > 0) {
            $this->no_guard_from_time = null;
            $this->no_guard_notification = 0;
            $this->save();
        }
    }

    public function AsterCall()
    {
        return $this->hasMany('App\AsterCall');
    }
}
