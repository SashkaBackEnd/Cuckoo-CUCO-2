<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class IncomeCall extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'call_date','phone_number', 'call_id', 'call_status', 'phone_input', 'process_status', 'guarded_object_id',
        'security_guard_id', 'action_type',
    ];

    public static function loadCalls()
    {
        $zvonok = new Zvonok();
        $calls = $zvonok->getIncomeCalls();
        $existingCallIds = IncomeCall::get('call_id')->pluck('call_id')->toArray();
        foreach ($calls as $call) {
            if (!in_array($call['call_id'], $existingCallIds)) {
                $ivrResult = '';
                foreach ($call['ivr_data'] as $choice) {
                    $ivrResult .= $choice['button_num'];
                }
                $callDate = Carbon::parse($call['completed'])->timestamp;
                $callDetails = [
                    'call_id' => $call['call_id'],
                    'phone_number' => $call['phone'],
                    'phone_input' => $ivrResult,
                    'call_status' => $call['status'],
                    'process_status' => 'created',
                    'call_date' => $callDate,
                ];
                IncomeCall::create($callDetails);
            }
        }
    }

    public static function handleNewCalls()
    {
        $newCalls = IncomeCall::where('process_status', 'created')->get();
        foreach ($newCalls as &$newCall) {
            //ищем объект
            $guardedObject = GuardedObject::where('phone', $newCall['phone_number'])->first();
            if (is_null($guardedObject)) {
                $newCall->action_type = 'wrongPhone';
                $newCall->process_status = 'done';
                $newCall->save();
                continue;
            }
            $pinFromCall = mb_substr($newCall['phone_input'], 0, 5);
            //ищем смену
            $workShift = $guardedObject->workShift->where('shift_status','process')->first();
            if (is_null($workShift)) {
                $securityGuard = SecurityGuard::where('pin', $pinFromCall)->first();
                if (is_null($securityGuard)) {
                    //охранник не найден
                    $newCall->guarded_object_id = $guardedObject->id;
                    $newCall->action_type = 'wrongPin';
                    $newCall->process_status = 'done';
                    $newCall->save();
                } else {
                    //начинаем смену
                    $data = [
                        'guarded_object_id' => $guardedObject->id,
                        'security_guard_id' => $securityGuard->id,
                        'shift_status' => 'process',
                        'start_time' => $newCall['call_date'],
                        'end_time' => null,
                    ];
                    WorkShift::create($data);
                    $newCall->guarded_object_id = $guardedObject->id;
                    $newCall->security_guard_id = $securityGuard->id;
                    $newCall->action_type = 'startShift';
                    $newCall->process_status = 'done';
                    $newCall->save();
                    Event::addShiftStartEvent($securityGuard->id, $guardedObject->id);
                }
            } else {
                $securityGuard = $workShift->securityGuard()->first();
                if (is_null($securityGuard)) {
                    //смене не присвоен охранник - невозможное событие
                } else {
                    if ($securityGuard->pin != $pinFromCall) {
                        //охранник не найден
                        $newCall->guarded_object_id = $guardedObject->id;
                        $newCall->action_type = 'wrongPin';
                        $newCall->process_status = 'done';
                        $newCall->save();
                    } else {
                        //завершаем смену
                        $workShift->shift_status = 'done';
                        $workShift->end_time = $newCall['call_date'];
                        $workShift->save();
                        $newCall->guarded_object_id = $guardedObject->id;
                        $newCall->security_guard_id = $securityGuard->id;
                        $newCall->action_type = 'endShift';
                        $newCall->process_status = 'done';
                        $newCall->save();
                        Event::addShiftEndEvent($securityGuard->id, $guardedObject->id);
                    }
                }
            }
        }
    }
}
