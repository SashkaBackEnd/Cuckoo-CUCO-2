<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\SMS\SMS;

class AsterCall extends Model
{
  public $timestamps = false;
  public const ATTEMPT_INTERVAL = 120;
  public const GUARDS_LIMIT_FOR_OBJECT = 10000;

  protected $fillable = [
    'guarded_object_id', 'process_status',
  ];

  //todo для того чтобы не менять старую логику обработки звонков для объектов с разными статусами
  // здесь можно будет разделить логику обработки
  public function handleCall()
  {
    if (!is_null($this->process_status)) {
      return;
    }
    //берем тип звонка
    //$rabbit = new Rabbit();
    // если sos
    $queuedCall = QueuedCall::find($this->queued_call_id);
    if ($queuedCall != null && $queuedCall->call_status == 'process_callback') {
      $this->handleIncomeCall();
    }

    if ($this->phone_input === '0' && $this->call_result === '8') {
      $this->handleSosCall();
    } elseif ($this->direction == 'in') {
      $this->handleIncomeCall();
    } elseif ($this->direction == 'out' || $this->direction == 'outcustom') {
      $this->handleOutcomeCall();
    }
  }

  public function guardedObject()
  {
    return $this->belongsTo('App\GuardedObject');
  }

  public function queuedCall()
  {
    return $this->hasOne('App\QueuedCall');
  }

  public static function handleNewCalls()
  {
    AsterCall::whereNull('process_status')->get()->each(function ($call) {
      $call->handleCall();
    });
  }

  public function handleIncomeCall()
  {
    // Ищем объект по номеру телефона. Если пост относится к объекту с центральным постом,
    // то охранник должен совершить звонок с контактного номера центрального поста.
    // Поэтому к событиям прикрепляется айди того поста, с которого был совершен звонок,
    // т.е с центрального.
    $guardedObject = GuardedObject::where('phone', '+' . $this->phone_number)->first();
    if (is_null($guardedObject)) { // Если не найден добавляем статус нет объекта
      $this->process_status = 'noObject';
      ActionLog::addToLogFromSystem('Был совершен звонок с неизвестного номера ' . $this->phone_number);
      $this->save();
      return;
    }

    if (!$guardedObject->entity->hasCentralPost()) {
      $pin = $this->phone_input;
    } elseif (strpos($this->phone_input, '*') !== false) {
      $pinAndFast = explode('*', $this->phone_input);
      $pinAndFast = array_filter($pinAndFast);
      $pinAndFast = array_values($pinAndFast);

      // TODO скорее всего эксепшен не будет выкинут, оставил на всякий случай
      try {
        $pin = $pinAndFast[0];
        $fast = $pinAndFast[1];
      } catch (Exception $exception) {
        $this->process_status = 'wrongPin';
        ActionLog::addToLogFromSystem("При получении пинкода произошла ошибка. Pin: $this->phone_input");
        $this->save();
        return null;
      }

      $guardedObject = GuardedObject::query()
        ->with('entity.centralPost')
        ->where('id', $fast)
        ->first();
      if(is_null($guardedObject)){
        $this->process_status = 'wrongPin';
        ActionLog::addToLogFromSystem("При получении пинкода произошла ошибка. Pin: $this->phone_input");
        $this->save();
        return;
      }
    } else {
      $this->process_status = 'wrongPin';
      $this->save();
      Event::create([
        'type' => 'wrongPin',
        'guarded_object_id' => $guardedObject->id,
        'security_guard_id' => 0
      ]);

      ActionLog::addToLogFromSystem("Указан неверный формат пинкода для объекта с центральным постом $this->phone_input");
      return;
    }

    $securityGuard = SecurityGuard::where('pin', DB::raw("'$pin'"))->first();
    $this->guarded_object_id = $guardedObject->id;

    if (is_null($securityGuard)) { // Если не найден

      // Введем пин-код два раза неверно
      $event = Event::where('guarded_object_id',$guardedObject->id)->orderBy('created_at', 'desc')->first();
      if ($event != null && $event->type == 'unknownPin') {
          $entity = Entity::where('id', $guardedObject->entity_id)->first();
          if ($entity != null) {
            $entity_user = EntityManager::where('entity_id', $entity->id)->get();
            foreach ($entity_user as $item) {
              $user = User::where('id', $item->user_id)->first();
              if ($user != null) {
                // Отправляем менеджерам смс
                $smsEventData = [
                  'type' => 'smsForHeadOfSecurity',
                  'guarded_object_id' => $guardedObject->id,
                  'security_guard_id' => 0,
                ];
                $phoneHOS = trim($user->phone, '+');
	            $text = $guardedObject->name . ' (' . $entity->name . '): дважды неверно введен пин-код';

                $sms = new SMS();
                $smsStatus = $sms->send($phoneHOS, $text);
                if ($smsStatus->status === "OK") {
                   Event::create($smsEventData);
                }
              }
            }
          }
          // Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
      } else {
          $nextCallTime = Carbon::now()->timestamp + 30;

          $queuedCallData = [
            'call_date' => $nextCallTime,
            'guarded_object_id' => $guardedObject->id,
            'call_status' => 'callback',
          ];
          QueuedCall::create($queuedCallData);
      }
      

      $this->process_status = 'noGuard'; // добавляем статус нет охранника
      $this->save();
      $eventData = [
        'security_guard_id' => 0,
        'guarded_object_id' => $guardedObject->id,
        'type' => 'unknownPin',
        'value_1' => $this->phone_input,
      ];
      Event::create($eventData);
      /*$rabbit = new Rabbit();
      $socketMessage = [
        'fetchObjects' => '',
        'fetchObjectById' => $guardedObject->id,
      ];
      $rabbit->sendForSocket(json_encode($socketMessage));*/
      return;
    }

    $eventData = [
      'security_guard_id' => $securityGuard->id,
      'guarded_object_id' => $guardedObject->id,
    ];
    $guardCurrentShift = $securityGuard->workShift->where('shift_status', 'process')->first();
    if (is_null($guardCurrentShift)) { // Если охранник без смены
      $currentObjectShifts = $guardedObject->workShift->where('shift_status', 'process');
      // если есть смена на объекте
      if ($currentObjectShifts->count() >= self::GUARDS_LIMIT_FOR_OBJECT) {
        //На объекте максимальное число охранников - смена не начата
        $this->process_status = 'guardsLimit';
        $this->save();
        //TODO Событие о лимите охранников
        return;
      }

      // Проверяем, если служебный работник, то просто записываем в события
      if ($securityGuard->status == SecurityGuard::STATUS_SERVICE) {
            $eventData = ['type' => 'checkPost', 'security_guard_id' => $securityGuard->id, 'guarded_object_id' => $guardedObject->id];
            $event = Event::create($eventData);
            $this->process_status = 'customCheckPassed';
            $this->save();
            return;
            return;
      }

      // создаем смену
      $workShiftData = [
        'guarded_object_id' => $guardedObject->id,
        'security_guard_id' => $securityGuard->id,
        'shift_status' => 'process',
        'start_time' => strtotime($this->call_date),
        'end_time' => null,
      ];
      WorkShift::create($workShiftData);
      $eventData['type'] = 'startShift';
      Event::create($eventData);
      $this->process_status = 'done';
      $guardedObject->number_of_call_attempts = 0;
      $guardedObject->save();
      if ($guardedObject->sos_status == 1) {
        $eventData['type'] = 'autoSosEnd';
        Event::create($eventData);
        $guardedObject->sos_status = 0;
        $guardedObject->number_of_call_attempts = 0;
        $guardedObject->save();
        $guardedObject->noteIfHasGuard();
      }
    } elseif ($guardCurrentShift->guarded_object_id != $guardedObject->id) { // иначе если смена охранника не на этом объекте
      $eventData['type'] = 'objectGuardMismatch';
      Event::create($eventData);

      //TODO переделать во втором спринте на менеджеров
//            $phoneHOS = trim($guardedObject->phone_hos, '+');
//            $text = $guardedObject->name . ' (' . $guardedObject->phone . '): пытался заступить охранник, работающий на другом посту. Охранник '
//                . $securityGuard->shortName . ' (' . $securityGuard->phone . ')';
//
//            $sms = new SMS();
//            $smsStatus = $sms->send($phoneHOS, $text);
//            Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//            if ($smsStatus->status === "OK") {
//                $eventData['type'] = 'smsForHeadOfSecurity';
//                Event::create($eventData);
//            }
      $this->process_status = 'objectGuardMismatch';
    } else { // иначе полностью завершаем смену
      $salary = (new SecurityGuardCalculateSalary($securityGuard))->calculateDoneSalary();
      $guardCurrentShift->shift_status = 'done';
      $guardCurrentShift->end_time = strtotime($this->call_date);
      $guardCurrentShift->salary = $salary->id ?? 0;
      $guardCurrentShift->save();
      $eventData['type'] = 'endShift';
      Event::create($eventData);
      if ($guardedObject->sos_status == 1) {
        $eventData['type'] = 'autoSosEnd';
        Event::create($eventData);
        $guardedObject->sos_status = 0;
        $guardedObject->number_of_call_attempts = 0;
        $guardedObject->save();
      }
      $this->process_status = 'done';
      $guardedObject->noteIfNoGuard();
    }
    $this->save();

    /*$rabbit = new Rabbit();
    $socketMessage = [
      'fetchObjects' => '',
      'fetchGuards' => '',
      'fetchObjectById' => $guardedObject->id,
      'fetchGuardById' => [$securityGuard->id],
    ];
    $rabbit->sendForSocket(json_encode($socketMessage));*/
  }

  // TODO здесь делать проверку после автоматического обзвона
  public function handleOutcomeCall()
  {

    $queuedCall = QueuedCall::find($this->queued_call_id);
    if (!is_null($queuedCall)) {
      $queuedCall->call_status = 'done';
      $queuedCall->save();
    }

    $guardedObject = $this->guardedObject()->first()->load('entity');

    if (!$guardedObject->entity->hasCentralPost()) {
      $pin = $this->phone_input;
    } elseif (strpos($this->phone_input, '*') !== false) {
      $pinAndFast = explode('*', $this->phone_input);

      try {
        $pin = $pinAndFast[0];
        $fast = $pinAndFast[1];
      } catch (Exception $exception) {
        $this->process_status = 'wrongPin';
        $this->save();
        Event::create([
          'type' => 'wrongPin',
          'guarded_object_id' => $guardedObject->id,
          'security_guard_id' => 0
        ]);
        ActionLog::addToLogFromSystem("При получении пинкода произошла ошибка. Pin: $this->phone_input");
        return;
      }

      if ($pin === '' || $fast === '') {
        $this->process_status = 'wrongPin';
        $this->save();
        Event::create([
          'type' => 'wrongPin',
          'guarded_object_id' => $guardedObject->id,
          'security_guard_id' => 0
        ]);
        ActionLog::addToLogFromSystem("Указан неверный формат пинкода для объекта с центральным постом $this->phone_input");
        return;
      }

      $guardedObject = GuardedObject::query()
        ->with('entity')
        ->where('id', $fast)
        ->first();
    } else {
      $this->process_status = 'wrongPin';
      $this->save();
      Event::create([
        'type' => 'wrongPin',
        'guarded_object_id' => $guardedObject->id,
        'security_guard_id' => 0
      ]);
      ActionLog::addToLogFromSystem("Указан неверный формат пинкода для объекта с центральным постом $this->phone_input");
      return;
    }

    if (!$guardedObject) {
      $this->process_status = 'noGuard';
      $this->save();
      return;
    }

    $currentShifts = $guardedObject->workShift()->where('shift_status', 'process')->get();
    if ($currentShifts->count() == 0) {
      // на объекте нет смены
      $this->process_status = 'noShift';
      $this->save();
      return;
    }

    $customCheck = $this->direction == 'outcustom';

    $securityGuards = $currentShifts->map(function ($shift) {
      return $shift->securityGuard()->first();
    });

    $eventData = [
      'guarded_object_id' => $this->guarded_object_id,
      'security_guard_id' => 0,
    ];
    $socketMessage = [
      'fetchObjects' => '',
      'fetchGuards' => '',
      'fetchObjectById' => $this->guarded_object_id,
    ];
    $callAttemptsSetting = $guardedObject->entity->call_back_quantity;
    // если результат не answered
    if ($this->call_result != 8) {
      // добавляем событие не ответил
      if ($customCheck) {
        $eventData['type'] = 'customCheckFailed';
        $this->process_status = 'noAnswer';
      } else {
        // добавляем в очередь новый звонок
        // TODO изменить генерацию новых звонков
        //  + проверка call_back_quantity
        // if ($guardedObject->number_of_call_attempts == 0) {
          $eventData['type'] = 'checkFailed';
        // } 
        if ($guardedObject->number_of_call_attempts < $callAttemptsSetting) {
          $guardedObject->number_of_call_attempts = $guardedObject->number_of_call_attempts + 1;
          $guardedObject->save();
          $nearestQueuedCalls = $guardedObject->queuedCalls()->where('call_status', 'queued')->orderBy('call_date', 'desc')->get();
          $nextCallTime = Carbon::parse($this->call_date)->timestamp + self::ATTEMPT_INTERVAL;
          foreach ($nearestQueuedCalls as $call) {
            if (Carbon::parse($call->call_date)->timestamp < $nextCallTime) {
              $call->delete();
            }
          }

          $queuedCallData = [
            'call_date' => $nextCallTime,
            'guarded_object_id' => $guardedObject->id,
            'call_status' => 'queued',
          ];
          QueuedCall::create($queuedCallData);
        } else {
          // $guardedObject->number_of_call_attempts = 0;
          $guardedObject->save();
          $smsEventData = [
            'type' => 'smsForHeadOfSecurity',
            'guarded_object_id' => $guardedObject->id,
            'security_guard_id' => 0,
          ];
          //TODO переделать во втором спринте на менеджеров
//                    $phoneHOS = trim($guardedObject->phone_hos, '+');
//                    $text = $guardedObject->name . ' (' . $guardedObject->phone . '): автопроверка не пройдена.';
//
//                    $sms = new SMS();
//                    $smsStatus = $sms->send($phoneHOS, $text);
//                    Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//                    if ($smsStatus->status === "OK") {
//                        Event::create($smsEventData);
//                    }
        }
        $this->process_status = 'noAnswer';
        Event::create($eventData);
        $this->save();
        return;
      }
    } else {
      $isPinCorrect = false;
      foreach ($securityGuards as $guard) {
        if ($guard->pin == $pin) {
          $isPinCorrect = true;
          $answeredGuard = $guard;
          break;
        }
      }
      if ($isPinCorrect) {
        //добавляем событие успешная проверка
        $eventData['security_guard_id'] = $answeredGuard->id;
        if ($customCheck) {
          $eventData['type'] = 'customCheckPassed';
        } else {
          $eventData['type'] = 'checkPassed';
        }
        $endSos = true;
        if ($guardedObject->number_of_call_attempts > 0) {
          $guardedObject->number_of_call_attempts = 0;
          $guardedObject->save();
        }
        //добавляем статус успешная проверка
        $this->process_status = 'done';
      } else {
        // добавляем событие ошибка пинкода
        $this->process_status = 'wrongPin';
        if ($customCheck) {
          $eventData['type'] = 'customCheckFailed';
        } else {

          $event = Event::where('guarded_object_id',$guardedObject->id)->orderBy('created_at', 'desc')->first();
          if ($event != null && $event->type == 'unknownPin') {
              $eventData['type'] = 'unknownPin';
              $entity = Entity::where('id', $guardedObject->entity_id)->first();
              if ($entity != null) {
                $entity_user = EntityManager::where('entity_id', $entity->id)->get();
                foreach ($entity_user as $item) {
                  $user = User::where('id', $item->user_id)->first();
                  if ($user != null) {
                    // Отправляем менеджерам смс
                    $smsEventData = [
                      'type' => 'smsForHeadOfSecurity',
                      'guarded_object_id' => $guardedObject->id,
                      'security_guard_id' => 0,
                    ];
                    $phoneHOS = trim($user->phone, '+');
                  $text = $guardedObject->name . ' (' . $entity->name . '): дважды неверно введен пин-код';

                    $sms = new SMS();
                    $smsStatus = $sms->send($phoneHOS, $text);
                    if ($smsStatus->status === "OK") {
                       Event::create($smsEventData);
                    }
                  }
                }
              }
              // Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
          } else {
              $eventData['type'] = 'checkFailed';
              if ($guardedObject->number_of_call_attempts < $callAttemptsSetting) {
                $guardedObject->number_of_call_attempts = $guardedObject->number_of_call_attempts + 1;
                $guardedObject->save();
                $nearestQueuedCalls = $guardedObject->queuedCalls()->where('call_status', 'queued')->orderBy('call_date', 'desc')->get();
                $nextCallTime = Carbon::parse($this->call_date)->timestamp + self::ATTEMPT_INTERVAL;
                foreach ($nearestQueuedCalls as $call) {
                  if (Carbon::parse($call->call_date)->timestamp < $nextCallTime) {
                    $call->delete();
                  }
                }
                $queuedCallData = [
                  'call_date' => $nextCallTime,
                  'guarded_object_id' => $guardedObject->id,
                  'call_status' => 'queued',
                ];
                QueuedCall::create($queuedCallData);
              } 
          }
        }
      }
    }
    Event::create($eventData);
    $this->save();
    if (isset($endSos) && $endSos) {
      if ($guardedObject->sos_status == 1) {
        $eventData['type'] = 'autoSosEnd';
        Event::create($eventData);
        $guardedObject->sos_status = 0;
        $guardedObject->save();
        $securityGuardIds = $currentShifts->map(function ($shift) {
          return $shift->securityGuard()->first()->id;
        });
        $socketMessage['fetchGuardById'] = $securityGuardIds;
      }
    }
    /*$rabbit = new Rabbit();
    $rabbit->sendForSocket(json_encode($socketMessage));*/
  }

  public function handleSosCall()
  {
    if ($this->direction == 'in') {
      $guardedObject = GuardedObject::where('phone', '+' . $this->phone_number)->first();
      $this->guarded_object_id = $guardedObject->id;
    } else {
      $guardedObject = $this->guardedObject()->first();
    }

    if (is_null($guardedObject)) { // Если не найден добавляем статус нет объекта
      $this->process_status = 'noObject';
      $this->save();
      return;
    }

    $guardedObject->sos_status = 1;
    $guardedObject->save();


    $entity = Entity::where('id', $guardedObject->entity_id)->first();
    if ($entity != null) {
        $entity_user = EntityManager::where('entity_id', $entity->id)->get();
        foreach ($entity_user as $item) {
            $user = User::where('id', $item->user_id)->first();
            if ($user != null) {
	            // Отправляем менеджерам смс
	            $smsEventData = [
	                'type' => 'smsForHeadOfSecurity',
	                'guarded_object_id' => $guardedObject->id,
	                'security_guard_id' => 0,
	            ];
	            $phoneHOS = trim($user->phone, '+');
	            $text = $guardedObject->name . ' (' . $entity->name . '): команда SOS';

	            $sms = new SMS();
	            $smsStatus = $sms->send($phoneHOS, $text);
	            if ($smsStatus->status === "OK") {
	                Event::create($smsEventData);
	            }
            }
        }
    }

    $eventData = [
      'type' => 'sos',
      'guarded_object_id' => $this->guarded_object_id,
      'security_guard_id' => 0,
    ];
    Event::create($eventData);
    $this->process_status = 'sos';
    $this->save();

    //TODO переделать во втором спринте на менеджеров
//        $phoneHOS = trim($guardedObject->phone_hos, '+');
//        $text = $guardedObject->name . ' (' . $guardedObject->phone . '): поступил сигнал SOS.';
//
//        $sms = new SMS();
//        $smsStatus = $sms->send($phoneHOS, $text);
//        Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//
//        if ($smsStatus->status === "OK") {
//            $eventData['type'] = 'smsForHeadOfSecurity';
//            Event::create($eventData);
//        }

//        if (!in_array($guardedObject->id, [1, 3])) { // TODO ЗДЕСЬ ID ТЕСТОВЫХ ОБЪЕКТОВ
//            $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
//            $generalPhones = [$settings['phone'], $settings['phone2'], $settings['phone3']];
//            $generalPhones = array_unique($generalPhones);
//            foreach ($generalPhones as $phoneNumber) {
//                $sms->send($phoneNumber, $text);
//                Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//            }
//        }
    $currentShifts = $guardedObject->workShift()->where('shift_status', 'process')->get();
    if ($currentShifts->count() > 0) {
      $securityGuardIds = $currentShifts->map(function ($shift) {
        return $shift->securityGuard()->first()->id;
      });
      $socketMessage['fetchGuardById'] = $securityGuardIds;
    }
    $socketMessage = [
      'fetchGuards' => '',
      'fetchObjects' => '',
      'fetchObjectById' => $this->guarded_object_id,
    ];
    /*$rabbit = new Rabbit();
    $rabbit->sendForSocket(json_encode($socketMessage));*/
  }

//
//    /**
//     * Проверка введенного пинкода охранником для объекта с центральным постом и без центрального поста.
//     *
//     * Формат пинкода:
//     *  PIN*НомерПоста - для объектов с центральным постом
//     *  PIN - для объектов без центрального поста
//     *
//     * @return SecurityGuard|null
//     */
//    public function handlePin(): ?SecurityGuard
//    {
//        if (strpos('*', $this->phone_input) === false) {
//            return SecurityGuard::where('pin', $this->phone_input)->first();
//        }
//
//        $pinAndFast = explode('*', $this->phone_input);
//
//        // TODO скорее всего эксепшен не будет выкинут, оставил на всякий случай
//        try {
//            $pin = $pinAndFast[0];
//            $fast = $pinAndFast[1];
//        } catch (Exception $exception) {
//            ActionLog::addToLogFromSystem("При получении пинкода произошла ошибка. Pin: $this->phone_input");
//            return null;
//        }
//
//        if ($pin === '' || $fast === '') {
//            ActionLog::addToLogFromSystem("Неверно введен пинкод для объекта с центральным постом. Pin: $this->phone_input");
//            return null;
//        }
//
//        if (!$guardedObject) {
//            ActionLog::addToLogFromSystem("Номер поста $fast, введенного в pin, не существует.");
//            return null;
//        }
//
//        $centralPost = $guardedObject->entity->centralPost;
//        if (!$centralPost) {
//            ActionLog::addToLogFromSystem("Введен неверный формат пинкода. У данного объекта {$guardedObject->entity->id} отсутствует центральный пост.");
//            return null;
//        }
//
//        $centralPostNumber = $centralPost->phone;
//        $callNumber = "+$this->phone_number";
//
//        if ($centralPostNumber !== $callNumber) {
//            ActionLog::addToLogFromSystem("Номер телефона, с которого был совершен звонок, не соответствует номеру центрального поста объекта {$guardedObject->entity->id}");
//            return null;
//        }
//
//        // Присвоение айди центрального поста.
//        // Т.к. использование формата "PIN*НомерПоста" подразумевает,
//        // что охранник звонит с центрального поста
//        $this->guarded_object_id = $centralPost->id;
//        return $securityGuard;
//    }
}
