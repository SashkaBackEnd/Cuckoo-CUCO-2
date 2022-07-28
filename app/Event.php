<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Entity;
use App\GuardedObject;
use App\EntityManager;
use App\User;

class Event extends Model
{
    use SoftDeletes;

    /**
     * События по типам
     */
    public const SECURITY_GUARDS = 1;
    public const OBJECTS = 2;
    public const MANAGERS = 3;

    /**
     * Статусы событий
     * @todo Статусы старой версии
     */
    public const SOS = 'sos'; // Команда SOS
    public const SOS_END = 'sosEnd'; // Тревога снята вручную
    public const END_SHIFT = 'endShift'; // Завершил работу
    public const DIAL_PAUSED = 'dialPaused'; // Обзвон приостановлен
    public const START_SHIFT = 'startShift'; // Заступил на пост
    public const CHECK_POST = 'checkPost'; // Заступил на пост
    public const UNKNOWN_PIN = 'unknownPin'; // Введен неверный пинкод
    public const AUTO_SOS_END = 'autoSosEnd'; // Тревога снята автоматически
    public const MISSED_CALL = '1MissedCall'; // Пропущенный звонок
    public const CHECK_PASSED = 'checkPassed'; // Автоматическая проверка поста прошла успешно
    public const CHECK_FAILED = 'checkFailed'; // Автоматическая проверка поста не пройдена
    public const DIAL_RESUMED = 'dialResumed'; // Обзвон возобновлен
    public const GUARD_ACTIVATE = 'guardActivate'; // Статус охранника изменен на активный
    public const SHIFT_CANCELED = 'shiftCanceled'; // Аннулирована смена
    public const SECOND_MISSED_CALL = '2MissedCall'; // Пропущенный повторный звонок
    public const GUARD_DEACTIVATE = 'guardDeactivate'; // Статус охранника изменен на неактивный
    public const TIMEOUT_END_SHIFT = 'timeoutEndShift'; // Смена завершена принудительно
    public const SHORT_END_SHIFT_TRY = 'shortEndShiftTry'; // Пытался завершить смену без сменщика
    public const CUSTOM_CHECK_PASSED = 'customCheckPassed'; // Ручная проверка пройдена
    public const CUSTOM_CHECK_FAILED = 'customCheckFailed'; // Ручная проверка провалена
    public const SHIFT_TIME_EXCEEDED = 'shiftTimeExceeded'; // Превышена максимальная продолжительность смены
    public const OBJECT_GUARD_MISMATCH = 'objectGuardMismatch'; // Охранник с одного поста пытается начать смену на другом посту
    public const NO_GUARD_TIME_EXCEEDED = 'noGuardTimeExceeded'; // Превышено время нахождения поста без охраны
    public const SMS_FOR_HEAD_OF_SECURITY = 'smsForHeadOfSecurity'; // Отправлено SMS-уведомление

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'security_guard_id', 'guarded_object_id', 'value_1'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    protected $appends = [];
    /**
     * Атрибуты, которые должны быть преобразованы в даты.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     *
     * Список возможных типов событий
     *
     * @var array
     */
    public $possibleEvents = [
        'shiftCanceled', 'smsForHeadOfSecurity', 'checkFailed', 'checkPassed', '1MissedCall', '2MissedCall',
        'startShift', 'endShift', 'dialPaused', 'dialResumed', 'unknownPin'
    ];

    public static $eventsIndependentOfSecurityGuard = [
        'dialPaused', 'dialResumed', 'unknownPin'
    ];

    public static $eventsIndependentOfGuardedObject = [
        'guardActivate', 'guardDeactivate',
    ];

    public function guardedObject()
    {
        return $this->belongsTo('App\GuardedObject')->withTrashed();
    }

    public function securityGuard()
    {
        return $this->belongsTo('App\SecurityGuard')->withTrashed();
    }

    /**
     * Структура данных для вывода в списке событий в отчетах
     * @return array
     */
    public function listInfo()
    {
        $result = [
            'id' => $this->id,
            'date' => Carbon::parse($this->created_at)->timestamp,
            'type' => $this->type,
            'value1' => $this->value_1,
        ];
        if ($this->security_guard_id == 0) {
            $result['securityGuard'] = null;
        } else {
            $result['securityGuard'] = optional($this->securityGuard)->infoForEvent();
        }
        if ($this->guarded_object_id == 0) {
            $result['guardedObject'] = null;
        } else {
            $post = GuardedObject::where('id',$this->guarded_object_id)->first();
            if ($post != null) {

                $result['entity'] = Entity::where('id',$post->entity_id)->first();
                $entity_manager = EntityManager::where('entity_id', $post->entity_id)->get();
                $data = [];
                foreach ($entity_manager as $value) {
                    $data[] = $value->user_id;
                }
   
                $result['manager'] = User::whereIn('id', $data)->where('role', 'manager')->get();
          
            }
            $result['guardedObject'] = optional($this->guardedObject)->infoForEvent();
        }
        return $result;
    }

    public function securityGuardInfo()
    {
        return [
            'id' => $this->id,
            'date' => Carbon::parse($this->created_at)->timestamp,
            'type' => $this->type,
            'value1' => $this->value_1,
            'guardedObject' => $this->guardedObject->infoForEvent(),
        ];
    }

    public function guardedObjectInfo()
    {
        return [
            'id' => $this->id,
            'date' => Carbon::parse($this->created_at)->timestamp,
            'type' => $this->type,
            'value1' => $this->value_1,
            'securityGuard' => $this->securityGuard->infoForEvent(),
        ];
    }

    public static function addShiftStartEvent($securityGuardId, $guardedObjectId)
    {
        return Event::addEvent('startShift', $securityGuardId, $guardedObjectId);
    }

    public static function addShiftEndEvent($securityGuardId, $guardedObjectId)
    {
        return Event::addEvent('endShift', $securityGuardId, $guardedObjectId);
    }

    public static function addEvent(string $event, $securityGuardId, $guardedObjectId)
    {
        if (in_array($event, self::$eventsIndependentOfSecurityGuard)) {
            $eventData = ['type' => $event, 'security_guard_id' => 0, 'guarded_object_id' => $guardedObjectId];
        } else {
            $eventData = ['type' => $event, 'security_guard_id' => $securityGuardId, 'guarded_object_id' => $guardedObjectId];
        }
        return Event::create($eventData);
    }

    public static function getEvent(int $type, Carbon $from = null, Carbon $to = null): EventInterface
    {
        switch ($type) {
            case self::SECURITY_GUARDS:
                return new SecurityGuardEventCalculate();
            case self::OBJECTS:
            case self::MANAGERS:
        }
    }

    /**
     * @param Carbon|null $fromDate
     * @param SecurityGuard|null $securityGuard
     * @param GuardedObject|null $guardedObject
     * @return int|null
     */
    public static function getCountDialingErrors(
        Carbon        $fromDate = null,
        SecurityGuard $securityGuard = null,
        GuardedObject $guardedObject = null
    ): ?int
    {
        return DB::table('events')
            ->selectRaw('COUNT(id) count_id')
            ->when($fromDate, function ($q) use ($fromDate) {
                return $q->where('created_at', '>=', $fromDate->toDateTimeString());
            })
            ->when($securityGuard, function ($q) use ($securityGuard) {
                return $q->where('security_guard_id', $securityGuard->id);
            })
            ->when($guardedObject, function ($q) use ($guardedObject) {
                return $q->where('guarded_object_id', $guardedObject->id);
            })
            ->whereIn('type', ['unknownPin', 'checkFailed', '1MissedCall', '2MissedCall'])
            ->value('count_id');
    }

    /**
     * Получить ошибочные типы событий
     * @return string[]
     */
    public static function getErrorsType(): array
    {
        return [
            self::UNKNOWN_PIN,
            self::MISSED_CALL,
            self::CHECK_FAILED,
            self::SHIFT_CANCELED,
            self::TIMEOUT_END_SHIFT,
            self::SECOND_MISSED_CALL,
            self::SHORT_END_SHIFT_TRY,
            self::CUSTOM_CHECK_FAILED,
            self::SHIFT_TIME_EXCEEDED,
            self::OBJECT_GUARD_MISMATCH,
            self::NO_GUARD_TIME_EXCEEDED,
        ];
    }

    /**
     * Получить ошибочные типы событий при дозвонах
     * @return string[]
     */
    public static function getCallInErrorsType(): array
    {
        return [
            self::UNKNOWN_PIN,
            self::MISSED_CALL,
            self::CHECK_FAILED,
            self::SECOND_MISSED_CALL,
            self::CUSTOM_CHECK_FAILED
        ];
    }

    /**
     * Относится ли тип события к ошибкам
     *
     * @param string $type
     * @return bool
     */
    public static function isErrorType(string $type): bool
    {
        switch ($type) {
            case self::UNKNOWN_PIN:
            case self::MISSED_CALL:
            case self::CHECK_FAILED:
            case self::SHIFT_CANCELED:
            case self::TIMEOUT_END_SHIFT:
            case self::SECOND_MISSED_CALL:
            case self::SHORT_END_SHIFT_TRY:
            case self::CUSTOM_CHECK_FAILED:
            case self::SHIFT_TIME_EXCEEDED:
            case self::OBJECT_GUARD_MISMATCH:
            case self::NO_GUARD_TIME_EXCEEDED:
                return true;
            default:
                return false;
        }
    }

    /**
     * Относится ли тип события к ошибкам при дозвоне
     *
     * @param string $type
     * @return bool
     */
    public static function isCallInError(string $type): bool
    {
        switch ($type) {
            case self::UNKNOWN_PIN:
            case self::MISSED_CALL:
            case self::CHECK_FAILED:
            case self::SECOND_MISSED_CALL:
            case self::CUSTOM_CHECK_FAILED:
                return true;
            default:
                return false;
        }
    }
}
