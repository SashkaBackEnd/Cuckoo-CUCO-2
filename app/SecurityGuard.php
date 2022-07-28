<?php

namespace App;

use App\Http\Resources\SecurityGuardSalaryResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Kajti\Verhoeff\Verhoeff;

class SecurityGuard extends Model
{
    use SoftDeletes;

    /**
     * Тип работы
     */
    public const WORK_TYPE_WATCH = 'вахта';
    public const WORK_TYPE_SHIFT = 'смены';

    /**
     * Статус служащего
     */
    public const STATUS_SERVICE = 'служебный';
    public const STATUS_COMMON = 'обычный';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'pin', 'surname', 'name', 'patronymic', 'birth_date', 'phone', 'license',
        'comment', 'active', 'left_tings', 'driving_license', 'car', 'medical_book', 'gun', 'debts', 'work_type',
        'license_rank', 'knew_about_us', 'work_type', 'status', 'license_to_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'deleted_at', 'created_at'
    ];

    protected $appends = ['currentShift', 'lastCheck'];
    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    protected $casts = [
        'birth_date' => 'datetime:Y-m-d',
    ];

    public function getCurrentShiftAttribute()
    {
        return [];
    }

    public function getLastCheckAttribute()
    {
        return [];
    }

    public function getLogAttribute()
    {
        return [];
    }

    public function Event()
    {
        return $this->hasMany('App\Event');
    }

    /**
     * Подбирает для добавляемого охранника id не содержащий нулей
     * @return int
     */
    public static function getNextId()
    {
        $maxId = self::withTrashed()->max('id');
        if ($maxId) {
            $newId = $maxId + 1;
        } else {
            $newId = 1000;
        }
        return (int)$newId;
    }

    /** Рассчитывает пин-код для охранника
     * Пин-код состоит из id хранника и контрольной цифры, рассчитанной по алгоритму Верхуффа
     * @param $id int id охранника
     * @return int пин-код
     */
    public static function calculatePin(int $id)
    {
        $verhoeff = new Verhoeff($id);
        return (int)($id . $verhoeff->getDigit());
    }

    public function doneSalary()
    {
        $salary = (new SecurityGuardCalculateSalary($this))->calculateDoneSalary();
        return $salary->salary;
    }

    public function infoForEvent()
    {
        $salary = (new SecurityGuardCalculateSalary($this))->calculateProcessSalary();
        return [
            'id' => $this->id,
            'name' => $this->fullName,
            'shortName' => $this->shortName,
            'pin' => $this->pin,
            'phone' => $this->phone,
            "active" => (int)$this->active,
            'salary' => $salary ? new SecurityGuardSalaryResource($salary) : null
        ];
    }

    public function getShortNameAttribute()
    {
        $result = $this->surname . ' ';
        if ($this->name != '') {
            $result .= Str::limit($this->name, 1, '.');
        }
        if ($this->patronymic != '') {
            $result .= Str::limit($this->patronymic, 1, '.');
        }
        return trim($result);
    }

    public function getFullNameAttribute()
    {
        $result = $this->surname . ' ';
        if ($this->name != '') {
            $result .= $this->name . ' ';
        }
        if ($this->patronymic != '') {
            $result .= $this->patronymic;
        }
        return trim($result);
    }

    public function fullInfo()
    {
        $currentShiftInfo = null;
        $currentShift = $this->workShift()->whereIn('shift_status', ['process', 'finishing', 'finishingTimeout'])->first();
        if ($currentShift) {
            $currentShiftInfo = $currentShift->securityGuardInfo();
        }
        return [
            "surname" => $this->surname,
            "name" => $this->name,
            "patronymic" => $this->patronymic,
            "birthDate" => Carbon::parse($this->birth_date)->format('Y-m-d'),
            "phone" => $this->phone,
            "license" => $this->license,
            "comment" => $this->comment,
            "id" => $this->id,
            "pin" => $this->pin,
            "currentShift" => $currentShiftInfo,
            "lastCheck" => $this->lastListCheck(),
            "log" => $this->event()->where('created_at', '>', Carbon::now()->subDay()->toDateTimeString())->get()->map->listInfo(),
            "active" => (int)$this->active,
        ];
    }

    /**
     * @todo вынес старую реализацию в метод
     * @todo на время
     * @return mixed
     */
    public function log()
    {
        return $this->event()
            ->where('created_at', '>', Carbon::now()->subDay()->toDateTimeString())
            ->orderBy('created_at', 'desc')
            ->get()->map->listInfo();
    }

    public function listInfo()
    {
        $objects = $this->currentAndLastObjects();
        $currentShiftInfo = null;
        $currentShift = $this->workShift()->whereIn('shift_status', ['process', 'finishing', 'finishingTimeout'])->first();
        if ($currentShift) {
            $currentShiftInfo = $currentShift->securityGuardInfo();
        }
        return [
            "id" => $this->id,
            "pin" => $this->pin,
            "surname" => $this->surname,
            "name" => $this->name,
            "patronymic" => $this->patronymic,
            "phone" => $this->phone,
            "currentObject" => $objects['current'],
            "lastObject" => $objects['last'],
            "lastCheck" => $this->lastListCheck(),
            "currentShift" => $currentShiftInfo,
            "active" => (int)$this->active,
        ];
    }

    public function currentAndLastObjects()
    {
        $lastObject = null;
        $currentObject = null;

        $currentShift = $this->workShift()->whereIn('shift_status', ['process', 'finishing'])->first();
        if ($currentShift) {
            $currentObject = $currentShift->guardedObject()->withTrashed()->first()->infoForEvent();
        } else {
            $lastShift = $this->workShift()->where('shift_status', 'done')->orWhere('shift_status', 'cancel')->orderBy('end_time', 'desc')->first();
            if ($lastShift) {
                $lastObject = $lastShift->guardedObject()->withTrashed()->first()->infoForEvent();
            }
        }
        return [
            'current' => $currentObject,
            'last' => $lastObject,
        ];
    }

    public function currentShift()
    {
        $currentShift = $this->workShift()->where('shift_status', 'process')->first();
        if ($currentShift) {
            return $currentShift->securityGuardInfo();
        } else {
            return null;
        }
    }

    public function lastListCheck()
    {
        $currentShift = $this->workShift()->where('shift_status', 'process')->first();
        if (is_null($currentShift)) {
            $lastCheck = null;
        } else {
            $lastCheck = $currentShift->asterCall->sortByDesc('call_date')->whereNotNull('process_status')->where('process_status', '!=', 'objectGuardMismatch')->first();
            if (!is_null($lastCheck)) {
                $sosCheck = $currentShift->asterCall->sortByDesc('call_date')->where('process_status', 'sos')->first();
                $guardedObject = $currentShift->guardedObject()->withTrashed()->first();
                if (!is_null($sosCheck) && $guardedObject->sos_status == 1) {
                    $lastCheck = [
                        'date' => Carbon::parse($sosCheck->call_date)->timestamp,
                        'type' => 'sos',
                    ];
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
            }
        }
        return $lastCheck;
    }

    public function workShift()
    {
        return $this->hasMany('App\WorkShift');
    }

    //TODO наброски отчета
//    /**
//     * @param Carbon|null $from
//     * @param Carbon|null $to
//     * @return SupportCollection
//     */
//    public static function getEventsStat(Carbon $from = null, Carbon $to = null): SupportCollection
//    {
//        $from = $from ? $from->toDateTimeString() : null;
//        $to = $to ? $to->toDateTimeString() : null;
//
//        return collect(DB::select("
//            WITH count_events as (SELECT count(e.id)
//                                   OVER (PARTITION BY e.type, e.security_guard_id, e.guarded_object_id) count_events,
//                             sg.id                                                 security_guards_id,
//                             e.guarded_object_id,
//                             e.type
//                      FROM events e
//                               JOIN security_guards sg ON e.security_guard_id = sg.id
//                      WHERE IF('$from' <> '', e.created_at >= '$from',
//                               e.created_at is not null)
//                        AND IF('$to' <> '', e.created_at <= '$to',
//                               e.created_at is not null))
//
//            SELECT *
//            FROM count_events
//            GROUP BY security_guards_id, guarded_object_id, type, count_events
//        "));
//    }
//
//    /**
//     * @param Carbon|null $from
//     * @param Carbon|null $to
//     * @return SupportCollection
//     */
//    public static function generateEventsStat(Carbon $from = null, Carbon $to = null): SupportCollection
//    {
//        $securityGuardsEvents = self::getEventsStat($from, $to);
//
//        $securityGuardsId = array_unique(data_get($securityGuardsEvents, '*.security_guards_id'));
//
//        $data = collect();
//
//        foreach ($securityGuardsId as $id) {
//            $events = $securityGuardsEvents->where('security_guards_id', $id);
//
//            $data[$id] = new SecurityGuardEventStruct();
//            foreach ($events as $event) {
//                if (!isset($data[$id]->posts[$event->guarded_object_id])) {
//                    $data[$id]->posts[$event->guarded_object_id] = new PostEventStruct($event->guarded_object_id);
//                }
//
//                $data[$id]->callCount += $event->count_events;
//                $data[$id]->posts[$event->guarded_object_id]->callCount += $event->count_events;
//
//                if (Event::isErrorType($event->type)) {
//                    $data[$id]->errorsCount += $event->count_events;
//                    $data[$id]->posts[$event->guarded_object_id]->errorsCount += $event->count_events;
//                }
//
//                if (Event::isCallInError($event->type)) {
//                    $data[$id]->callInErrors += $event->count_events;
//                    $data[$id]->posts[$event->guarded_object_id]->callInErrors += $event->count_events;
//                }
//
//                if ($event->type === Event::UNKNOWN_PIN ||
//                    $event->type === Event::OBJECT_GUARD_MISMATCH) {
//                    $data[$id]->startShiftErrors += $event->count_events;
//                    $data[$id]->reStartShiftErrors += $event->count_events;
//                    $data[$id]->posts[$event->guarded_object_id]->startShiftErrors += $event->count_events;
//                    $data[$id]->posts[$event->guarded_object_id]->reStartShiftErrors += $event->count_events;
//                }
//
//                if ($event->type === Event::SHIFT_TIME_EXCEEDED) {
//                    $data[$id]->overtimeShiftErrors += $event->count_events;
//                    $data[$id]->posts[$event->guarded_object_id]->overtimeShiftErrors += $event->count_events;
//                }
//
//            }
//        }
//
//        return $data;
//    }
//
//    /**
//     * @param Carbon|null $from
//     * @param Carbon|null $to
//     * @return array
//     */
//    public function getEvent(Carbon $from = null, Carbon $to = null)
//    {
//
//        $data = [];
//
//        foreach ($securityGuards as $securityGuard) {
//            $securityEvents = $events
//                ->where('security_guards_id', $securityGuard->id);
//
//            if (!$securityEvents) {
//                continue;
//            }
//
//
//            foreach ($securityEvents as $event) {
//
//            }
//        }
//    }
//
//    public static function generateEvent(Carbon $from = null, Carbon $to = null): SupportCollection
//    {
//
//    }
}
