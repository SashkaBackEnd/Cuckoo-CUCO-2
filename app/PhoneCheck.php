<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PhoneCheck extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'call_id', 'phone_number', 'guarded_object_id', 'security_guard_id', 'call_status'
    ];

    public function checkCall()
    {
        if ($this->call_status == 'created') {
            $zvonok = new Zvonok();
            $callData = $zvonok->getCallInfo($this->call_id);
            $callData = $callData[0];
            if (isset($callData['dial_status']) && in_array($callData['dial_status'], [3])) { // 3 - "Не дозвонились"
                $eventData = ['type' => '1MissedCall', 'security_guard_id' => $this->security_guard_id, 'guarded_object_id' => $this->guarded_object_id];
                $this->call_status = '1MissedCall';
                $this->save();
                Event::create($eventData);
            }
            if (isset($callData['dial_status']) && in_array($callData['dial_status'], [2, 5, 8, 10, 17])) {
                // 2 - "Абонент сбросил звонок" 5 - "Абонент ответил" 8 - "Невалидная кнопка"
                // 10 - 'Завершен без действия клиента' 17 - "По максимальной продложительности звонка"
                // 19 - Прослушал ролик и не нажал кнопку 21 - Событие по нажатию кнопки и продолжительности разговора
                // 22 - Событие по продолжительности разговора
                if (isset($callData['ivr_data'])) {
                    $count = 1;
                    $ivrResult = '';
                    foreach ($callData['ivr_data'] as $choice) {
                        if ($count > 5) {
                            break;
                        }
                        $ivrResult .= $choice['button_num'];
                        $count++;
                    }

                    $eventData = ['security_guard_id' => $this->security_guard_id, 'guarded_object_id' => $this->guarded_object_id];

                    $guardPin = $this->securityGuard()->first()->pin;
                    if ($ivrResult == $guardPin) {
                        $this->call_status = 'checkPassed';
                        $eventData['type'] = 'checkPassed';
                    } else {
                        $this->call_status = 'checkFailed';
                        $eventData['type'] = 'checkFailed';
                    }
                    $this->phone_input = $ivrResult;
                    $this->save();
                    Event::create($eventData);
                    /*$rabbit = new \App\Rabbit();
                    $rabbit->sendForSocket('event');*/
                }
            }
        }
    }

    public static function checkAllCalls()
    {
        self::with('securityGuard')->where('call_status', 'created')->get()->each->checkCall();
    }

    public function securityGuard()
    {
        return $this->belongsTo('App\SecurityGuard');
    }

    public function workShift()
    {
        return $this->belongsTo('App\WorkShift');
    }
}
