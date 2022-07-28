<?php

namespace App;

use App\SMS\SMS;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class WorkShift extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'start_time', 'end_time', 'security_guard_id', 'guarded_object_id', 'shift_status', 'finishing_time', 'salary'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    protected $dates = [
        'start_time', 'end_time', 'created_at', 'updated_at', 'deleted_at', 'finishing_time',
    ];

    public $possibleStatuses = [
        'process', 'done', 'cancel', 'finishing'
    ];

    public static function getCurrentShiftForGuard($securityGuardId)
    {
        return WorkShift::where('security_guard_id', $securityGuardId)->whereIn('shift_status', ['process', 'finishing'])->first();
    }

    public static function getCurrentShiftForObject($guardedObjectId)
    {
        return WorkShift::where('guarded_object_id', $guardedObjectId)->where('shift_status', 'process')->first();
    }

    public function guardedObject()
    {
        return $this->belongsTo('App\GuardedObject');
    }

    public function guardedObjectTrashed()
    {
        return $this->belongsTo('App\GuardedObject')->withTrashed()->with('entity')->first();
    }

    public function securityGuard()
    {
        return $this->belongsTo('App\SecurityGuard');
    }

    public function info()
    {
        return [
            'id' => $this->id,
            'startDate' => Carbon::parse($this->start_time)->timestamp,
            'endDate' => is_null($this->end_time) ? null : Carbon::parse($this->end_time)->timestamp,
            'status' => $this->shift_status,
            'securityGuard' => $this->securityGuard()->withTrashed()->first()->infoForEvent(),
            'guardedObject' => $this->guardedObject()->withTrashed()->first()->infoForEvent(),
        ];
    }

    public function securityGuardInfo()
    {
        return [
            'id' => $this->id,
            'startDate' => Carbon::parse($this->start_time)->timestamp,
            'guardedObject' => $this->guardedObject()->withTrashed()->first()->infoForEvent(),
            'status' => $this->shift_status,
        ];
    }

    public function guardedObjectInfo()
    {
        return [
            'id' => $this->id,
            'startDate' => Carbon::parse($this->start_time)->timestamp,
            'endDate' => is_null($this->end_time) ? null : Carbon::parse($this->end_time)->timestamp,
            'status' => $this->shift_status,
            'securityGuard' => optional($this->securityGuard()->withTrashed()->first())->infoForEvent(),
            'guardedObject' => optional($this->guardedObject()->withTrashed()->first())->infoForEvent(),
        ];
    }

    public function phoneChecks()
    {
        return $this->hasMany('App\PhoneCheck');
    }

    public function asterCall()
    {
        return $this->hasMany('App\AsterCall');

    }

    public static function checkFinishingShifts()
    {
        $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
        $shiftChangeTime = intval($settings['shift_change_time']) * 60;
        $finishingShifts = WorkShift::where('shift_status', 'finishing')->get();
        foreach ($finishingShifts as $shift) {
            $shift->checkShiftFinish($shiftChangeTime);
        }
    }

    public function checkShiftFinish($shiftChangeTime = false)
    {
        if (!$shiftChangeTime) {
            $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
            $shiftChangeTime = intval($settings['shift_change_time']) * 60;
        }
        if (Carbon::parse($this->finishing_time)->timestamp + $shiftChangeTime + 60 < time()) {
            $guardedObject = $this->guardedObject()->first();
            $securityGuard = $this->securityGuard()->first();
            $this->shift_status = 'done';
            $this->end_time = time();
            $this->save();
            $eventData = [
                'type' => 'timeoutEndShift',
                'security_guard_id' => $securityGuard->id,
                'guarded_object_id' => $guardedObject->id
            ];
            Event::create($eventData);
            //TODO переделать во втором спринте на менеджеров
//            $phoneHOS = trim($guardedObject->phone_hos, '+');
//            $text = $guardedObject->name . '" смена не завершена вовремя. Охранник' . $securityGuard->shortName;
//
//            $sms = new SMS();
//            $smsStatus = $sms->send($phoneHOS, $text);
//
//            Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//            if ($smsStatus->status === "OK") {
//                $eventData['type'] = 'smsForHeadOfSecurity';
//                Event::create($eventData);
//            }
            $socketMessage = [
                'fetchObjectById' => $guardedObject->id,
                'fetchGuardById' => [$securityGuard->id],
            ];
            /*$rabbit = new Rabbit();
            $rabbit->sendForSocket(json_encode($socketMessage));*/
        }
    }

    public static function checkExceededShifts()
    {
        $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
        $exceededTime = Carbon::createFromTimestamp(time() - ((int)$settings['maximum_shift_time']) * 3600)->toDateTimeString();
        $exceededShifts = WorkShift::where('start_time', '<=', $exceededTime)->where('shift_status', 'process')->where('exceed_time_notification', 0)->get();
        foreach ($exceededShifts as $exceededShift) {
            $guardedObject = $exceededShift->guardedObject()->first();
            $securityGuard = $exceededShift->securityGuard()->first();
            $exceededShift->exceed_time_notification = 1;
            $exceededShift->save();
            $eventData = [
                'type' => 'shiftTimeExceeded',
                'security_guard_id' => $securityGuard->id,
                'guarded_object_id' => $guardedObject->id,
            ];
            Event::create($eventData);
            //TODO переделать во втором спринте на менеджеров
//            $phoneHOS = trim($guardedObject->phone_hos, '+');
//            $text = $guardedObject->name . ' (' . $guardedObject->phone . '): превышена максимальная продолжительность смены. Охранник '
//                . $securityGuard->shortName . ' (' . $securityGuard->phone . ')';
//
//            $sms = new SMS();
//            $smsStatus = $sms->send($phoneHOS, $text);
//
//            Telegram::sendMessage(env('TELEGRAM_DEBUG_CHAT_ID'), $text);
//            if ($smsStatus->status === "OK") {
//                $eventData['type'] = 'smsForHeadOfSecurity';
//                Event::create($eventData);
//            }

            /*$rabbit = new Rabbit();
            $socketMessage = [
                'fetchObjects' => '',
                'fetchGuards' => '',
                'fetchObjectById' => $guardedObject->id,
                'fetchGuardById' => [$securityGuard->id],
            ];
            $rabbit->sendForSocket(json_encode($socketMessage));*/
        }
    }
    public function shiftEventCounts()
    {
        $eventsList = Event::select('type', DB::raw('count(*) as count'))
            ->where(function ($query) {
                $query->where(function ($queryIn) {
                    $queryIn->where('guarded_object_id', $this->guardedObject()->withTrashed()->first()->id)
                        ->whereNotIn('type', ['objectGuardMismatch', 'shiftTimeExceeded']);
                })->orWhere('security_guard_id', $this->securityGuard()->withTrashed()->first()->id);
            })
            ->where('created_at', '>=', $this->start_time)
            ->where('created_at', '<=', $this->end_time)
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');
        return $eventsList;
    }

    public function getWorkHours()
    {
        $minutes = Carbon::parse($this->start_time)->diffInMinutes(Carbon::parse($this->end_time));
        return (int) round($minutes / 60);
    }
    public function getWorkHoursString()
    {
        $minutes = Carbon::parse($this->start_time)->diffInMinutes(Carbon::parse($this->end_time));
		$h =  (int) round($minutes / 60);
		$m = $minutes%60;
        return $h.' часа '.$m.' мин';
    }
    public static function getGuardsReportExcel($from,$to) {
        $color_column = [];
        $report = WorkShift::getGuardsReport($from, $to);

        $report_row = [];
        $report_row[] = [
            'Имя сотрудника',
            'Название объекта',
            'Название поста',
            'Кол-во смен',
            "Рабочее время" .chr( 13 ) .chr( 10 ) ."абонента",
            "Заработная плата " .chr( 13 ) .chr( 10 ) ." абонента",
            'Количество звонков',
            'Общее количество ошибок',
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." при дозвоне",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." заступления " .chr( 13 ) .chr( 10 ) ." на смену",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." повторного ".chr( 13 ) .chr( 10 ) ."  заступления",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." превышения времени"
        ];
        $count = 1;
        foreach ($report as $k => $rep) {
            $report_row[$count][] = $rep['name'];
            $report_row[$count][] = '';
            $report_row[$count][] = '';
            $report_row[$count][] = count($rep['shifts']);
            $report_row[$count][] = $rep['totalWorkHours'].' ч.';
            $report_row[$count][] = ($rep['salary'] == 0 ) ? "0" : $rep['salary'];
            $report_row[$count][] = ($rep['totalCalls'] == 0 ) ? "0" : $rep['totalCalls'];
            $report_row[$count][] = ($rep['totalErrors'] == 0 ) ? "0" : $rep['totalErrors'];
            $report_row[$count][] = ($rep['caseMissed'] == 0 ) ? "0" : $rep['caseMissed'];
            $report_row[$count][] = ($rep['caseShirtError'] == 0 ) ? "0" : $rep['caseShirtError'];
            $report_row[$count][] = ($rep['caseObjectGuardMismatch'] == 0 ) ? "0" : $rep['caseObjectGuardMismatch'];
            $report_row[$count][] = ($rep['caseShiftTimeExceed'] == 0 ) ? "0" : $rep['caseShiftTimeExceed'];
            $color_column[] = $count + 1;
            $count++;
            // Ищем смены
            foreach ($rep['shifts'] as $key => $value) {
                $report_row[$count][] = '';
                $report_row[$count][] = $value['object_name'];
                $report_row[$count][] = $value['name'];
                $report_row[$count][] = $value['totalDoneShifts'];
                $report_row[$count][] = $value['totalWorkHoursString'];
                $report_row[$count][] = ($value['salary'] == 0 ) ? "0" : $value['salary'];
                $report_row[$count][] = ($value['totalCalls'] == 0 ) ? "0" : $value['totalCalls'];
                $report_row[$count][] = ($value['totalErrors'] == 0 ) ? "0" : $value['totalErrors'];
                $report_row[$count][] = ($value['caseMissed'] == 0 ) ? "0" : $value['caseMissed'];;
                $report_row[$count][] = ($value['caseShirtError'] == 0 ) ? "0" : $value['caseShirtError'];
                $report_row[$count][] = ($value['caseObjectGuardMismatch'] == 0 ) ? "0" : $value['caseObjectGuardMismatch'];
                $report_row[$count][] = ($value['caseShiftTimeExceed'] == 0 ) ? "0" : $value['caseShiftTimeExceed'];
                $count++;
            }
        }
        return [$report_row,$color_column];
    }
    public static function getGuardsReport($from, $to)
    {

        $shifts = WorkShift::where('shift_status', 'done')
            ->with("guardedObject")
            ->where('end_time', '>=', Carbon::parse($from)->startOfMinute()->toDateTimeString())
            ->where('end_time', '<=', Carbon::parse($to)->addHour(24)->endOfMinute()->toDateTimeString())
            ->get()
            ->groupBy('security_guard_id');
        $report = [];
        $securityGuards = SecurityGuard::withTrashed()->get();
        foreach ($shifts as $guardId => $guardShifts) {
            foreach ($securityGuards as $securityGuard) {
                if ($securityGuard->id == $guardId) {
                    $guardReport = [
                        'id' => $guardId,
                        'name' => $securityGuard->fullName,
                        'shortName' => $securityGuard->shortName,
                        'totalWorkHours' => 0,
                        'totalDoneShifts' => count($guardShifts),
                        'shifts' => [],
                        'totalCalls' => 0,
                        'totalEmergencyCases' => 0,
                        'caseMissed' => 0,
                        'caseShiftChange' => 0,
                        'caseObjectGuardMismatch' => 0,
                        'caseShiftTimeExceed' => 0,
                        'caseShirtError' => 0,
                        'salary' => 0,
                        'totalErrors' => 0
                    ];
                    foreach ($guardShifts as $guardShift) {
                        if ($guardShift->guardedObject == null) {
                            continue;
                        }
                    	if ($guardShift->getWorkHours() == 0) {
                        	continue;
                    	}
                        $shift = [
                            'id' => $guardShift->guardedObject->id,
                            'object_name' => $guardShift->guardedObject->entity->name,
                            'name' => $guardShift->guardedObject->name,
                            'startTime' => Carbon::parse($guardShift->start_time)->timestamp,
                            'endTime' => Carbon::parse($guardShift->end_time)->timestamp,
                            'totalWorkHours' => $guardShift->getWorkHours(),
                            'totalWorkHoursString' => $guardShift->getWorkHoursString(),
                            'totalDoneShifts' => 1,
                            'totalCalls' => 0,
                            'totalEmergencyCases' => 0,
                            'caseMissed' => 0,
                            'caseShiftChange' => 0,
                            'caseObjectGuardMismatch' => 0,
                            'caseShiftTimeExceed' => 0,
                            'caseShirtError' => 0,
                            'salary' => $guardShift->salary,
                            'totalErrors' => 0
                        ];
                        $guardReport['totalWorkHours'] += $shift['totalWorkHours'];
                        $guardReport['salary'] += $shift['salary'];
                        $eventCounts = $guardShift->shiftEventCounts();
                        foreach ($eventCounts as $type => $count) {
                            switch ($type) {
                                case('checkPassed'):
                                    // no break
                                case('customCheckPassed'):
                                    $guardReport['totalCalls'] += $count;
                                    $shift['totalCalls'] += $count;
                                    break;
                                case('1MissedCall'):
                                    // no break
                                case('2MissedCall'):
                                    // no break
                                case('checkFailed'):
                                    // no break
                                case('customCheckFailed'):
                                    $guardReport['caseMissed']+= $count;
                                    $shift['caseMissed']+= $count;
                                    $guardReport['totalEmergencyCases']+= $count;
                                    $shift['totalEmergencyCases']+= $count;
                                    $guardReport['totalCalls']+= $count;
                                    $shift['totalCalls']+= $count;
                                    $guardReport['totalErrors']+= $count;
                                    $shift['totalErrors']+= $count;
                                    break;
                                case('unknownPin'):
                                    $guardReport['caseShirtError']+= $count;
                                    $shift['caseShirtError']+= $count;
                                    $guardReport['totalErrors']+= $count;
                                    $shift['totalErrors']+= $count;
                                    break;
                                case('objectGuardMismatch'):
                                    $guardReport['caseObjectGuardMismatch']+= $count;
                                    $shift['caseObjectGuardMismatch']+= $count;
                                    $guardReport['totalEmergencyCases']+= $count;
                                    $shift['totalEmergencyCases']+= $count;
                                    $guardReport['totalErrors']+= $count;
                                    $shift['totalErrors']+= $count;
                                    break;
                                case('shiftTimeExceeded'):
                                    $guardReport['caseShiftTimeExceed']+= $count;
                                    $shift['caseShiftTimeExceed']+= $count;
                                    $guardReport['totalEmergencyCases']+= $count;
                                    $shift['totalEmergencyCases']+= $count;
                                    $guardReport['totalErrors']+= $count;
                                    $shift['totalErrors']+= $count;
                                    break;
                                default:
                            }
                        }
                        $guardReport['shifts'][] = $shift;
                    }
                    $report[] = $guardReport;
                }
            }
        }
        return $report;
    }

    public static function getObjectsReportExcel($from,$to) {
        $report = WorkShift::getObjectsReport($from, $to);
 		$color_column = [];
        $report_row = [];
        $report_row[] = [
            'Имя сотрудника',
            'Название объекта',
            'Кол-во смен',
            "Рабочее время" .chr( 13 ) .chr( 10 ) ."абонента",
            "Заработная плата " .chr( 13 ) .chr( 10 ) ." абонента",
            'Количество звонков',
            'Общее количество ошибок',
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." при дозвоне",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." заступления " .chr( 13 ) .chr( 10 ) ." на смену",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." повторного ".chr( 13 ) .chr( 10 ) ."  заступления",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." превышения времени"
        ];
        $count = 1;
        foreach ($report as $k => $rep) {
            $report_row[$count][] = '';
            $report_row[$count][] = $rep['name'];
            $report_row[$count][] = count($rep['shifts']);
            $report_row[$count][] = $rep['totalWorkHours'].' ч.';
            $report_row[$count][] = ($rep['salary'] == 0 ) ? "0" : $rep['salary'];
            $report_row[$count][] = ($rep['totalCalls'] == 0 ) ? "0" : $rep['totalCalls'];
            $report_row[$count][] = ($rep['totalErrors'] == 0 ) ? "0" : $rep['totalErrors'];
            $report_row[$count][] = ($rep['caseMissed'] == 0 ) ? "0" : $rep['caseMissed'];
            $report_row[$count][] = ($rep['caseShirtError'] == 0 ) ? "0" : $rep['caseShirtError'];
            $report_row[$count][] = ($rep['caseObjectGuardMismatch'] == 0 ) ? "0" : $rep['caseObjectGuardMismatch'];
            $report_row[$count][] = ($rep['caseShiftTimeExceed'] == 0 ) ? "0" : $rep['caseShiftTimeExceed'];
            $color_column[] = $count + 1;
            $count++;
            // Ищем смены
            foreach ($rep['shifts'] as $key => $value) {
                $report_row[$count][] = '';
                $report_row[$count][] = $value['fullName'];
                $report_row[$count][] = $value['totalDoneShifts'];
                $report_row[$count][] = $value['totalWorkHoursString'];
                $report_row[$count][] = ($value['salary'] == 0 ) ? "0" : $value['salary'];
                $report_row[$count][] = ($value['totalCalls'] == 0 ) ? "0" : $value['totalCalls'];
                $report_row[$count][] = ($value['totalErrors'] == 0 ) ? "0" : $value['totalErrors'];
                $report_row[$count][] = ($value['caseMissed'] == 0 ) ? "0" : $value['caseMissed'];;
                $report_row[$count][] = ($value['caseShirtError'] == 0 ) ? "0" : $value['caseShirtError'];
                $report_row[$count][] = ($value['caseObjectGuardMismatch'] == 0 ) ? "0" : $value['caseObjectGuardMismatch'];
                $report_row[$count][] = ($value['caseShiftTimeExceed'] == 0 ) ? "0" : $value['caseShiftTimeExceed'];
                $count++;
            }
        }
        return [$report_row,$color_column];
    }
    public static function getObjectsReport($from, $to)
    {
        $shifts = WorkShift::where('shift_status', 'done')
            ->with("securityGuard")
            ->where('end_time', '>=', Carbon::parse($from)->startOfMinute()->toDateTimeString())
            ->where('end_time', '<=', Carbon::parse($to)->addHour(24)->endOfMinute()->toDateTimeString())
            ->get()
            ->groupBy('guarded_object_id');
        $report = [];
        $entities = Entity::all();
        foreach ($entities as $entity) {
            $object_report = [
                'id' => $entity->id,
                'name' => $entity->name,
                'totalWorkHours' => 0,
                'totalDoneShifts' => 0,
                'shifts' => [],
                'totalCalls' => 0,
                'totalEmergencyCases' => 0,
                'caseMissed' => 0,
                'caseShiftChange' => 0,
                'caseObjectGuardMismatch' => 0,
                'caseShiftTimeExceed' => 0,
                'caseShirtError' => 0,
                'totalErrors' => 0,
                'salary' => 0
            ];
            $guardedObjects = GuardedObject::withTrashed()->get();
            foreach ($shifts as $objectId => $guardShifts) {
                if (GuardedObject::withTrashed()->find($objectId) == null) {
                    continue;
                }
                if ($entity->id == GuardedObject::withTrashed()->find($objectId)->entity_id) {
                    foreach ($guardedObjects as $guardedObject) {
                        if ($guardedObject->id == $objectId) {
                            $guardReport = [
                                'id' => $objectId,
                                'name' => $guardedObject->name,
                                'totalWorkHours' => 0,
                                'totalDoneShifts' => count($guardShifts),
                                'shifts' => [],
                                'totalCalls' => 0,
                                'totalEmergencyCases' => 0,
                                'caseMissed' => 0,
                                'caseShiftChange' => 0,
                                'caseObjectGuardMismatch' => 0,
                                'caseShiftTimeExceed' => 0,
                                'caseShirtError' => 0,
                                'totalErrors' => 0,
                                'salary' => 0
                            ];
                            foreach ($guardShifts as $guardShift) {
                                if ($guardShift->getWorkHours() == 0) {
                                    continue;
                                }
                                $shift = [
                                    'id' => $guardShift->securityGuard()->withTrashed()->first()->id,
                                    'fullName' => $guardShift->securityGuard()->withTrashed()->first()->fullName,
                                    'shortName' => $guardShift->securityGuard()->withTrashed()->first()->shortName,
                                    'startTime' => Carbon::parse($guardShift->start_time)->timestamp,
                                    'endTime' => Carbon::parse($guardShift->end_time)->timestamp,
                                    'totalWorkHours' => $guardShift->getWorkHours(),
                                    'totalWorkHoursString' => $guardShift->getWorkHoursString(),
                                    'salary' => $guardShift->salary,
                                    'totalDoneShifts' => 1,
                                    'totalCalls' => 0,
                                    'totalEmergencyCases' => 0,
                                    'caseMissed' => 0,
                                    'caseShiftChange' => 0,
                                    'caseObjectGuardMismatch' => 0,
                                    'caseShiftTimeExceed' => 0,
                                    'caseShirtError' => 0,
                                    'totalErrors' => 0
                                ];
                                $guardReport['totalWorkHours'] += $shift['totalWorkHours'];
                                $guardReport['salary'] += $shift['salary'];
                                $eventCounts = $guardShift->shiftEventCounts();
                                foreach ($eventCounts as $type => $count) {
                                    switch ($type) {
                                        case('checkPassed'):
                                            // no break
                                        case('customCheckPassed'):
                                            $guardReport['totalCalls'] += $count;
                                            $shift['totalCalls'] += $count;
                                            break;
                                        case('1MissedCall'):
                                            // no break
                                        case('2MissedCall'):
                                            // no break
                                        case('checkFailed'):
                                            // no break
                                        case('unknownPin'):
                                            $guardReport['caseShirtError']+= $count;
                                            $shift['caseShirtError']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        case('customCheckFailed'):
                                            $guardReport['caseMissed']+= $count;
                                            $shift['caseMissed']+= $count;
                                            $guardReport['totalEmergencyCases']+= $count;
                                            $shift['totalEmergencyCases']+= $count;
                                            $guardReport['totalCalls']+= $count;
                                            $shift['totalCalls']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        case('objectGuardMismatch'):
                                            $guardReport['caseObjectGuardMismatch']+= $count;
                                            $shift['caseObjectGuardMismatch']+= $count;
                                            $guardReport['totalEmergencyCases']+= $count;
                                            $shift['totalEmergencyCases']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        case('shiftTimeExceeded'):
                                            $guardReport['caseShiftTimeExceed']+= $count;
                                            $shift['caseShiftTimeExceed']+= $count;
                                            $guardReport['totalEmergencyCases']+= $count;
                                            $shift['totalEmergencyCases']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        default:
                                    }
                                }
                                $guardReport['shifts'] = $shift;
                            }
                        }
                    }
                    $object_report['shifts'][] = $guardReport['shifts'];
                    $object_report['totalWorkHours'] += $guardReport['totalWorkHours'];
                    $object_report['totalDoneShifts'] += $guardReport['totalDoneShifts'];
                    $object_report['totalEmergencyCases'] += $guardReport['totalEmergencyCases'];
                    $object_report['caseMissed'] += $guardReport['caseMissed'];
                    $object_report['caseShiftChange'] += $guardReport['caseShiftChange'];
                    $object_report['caseObjectGuardMismatch'] += $guardReport['caseObjectGuardMismatch'];
                    $object_report['caseShiftTimeExceed'] += $guardReport['caseShiftTimeExceed'];
                    $object_report['caseShirtError'] += $guardReport['caseShirtError'];
                    $object_report['totalErrors'] += $guardReport['totalErrors'];
                    $object_report['salary'] += $guardReport['salary'];
                }
                $report[] = $object_report;
            }
        }
        return $report;
    }
    public static function getManagersReport($from, $to)
    {
        $users = User::where('role_type', 2)->get();
        $shifts = WorkShift::where('shift_status', 'done')
                ->where('end_time', '>=', Carbon::parse($from)->startOfMinute()->toDateTimeString())
                ->where('end_time', '<=', Carbon::parse($to)->addHour(24)->endOfMinute()->toDateTimeString())
                ->get()
                ->groupBy('guarded_object_id');
        $report = [];
        foreach ($users as $user) {
            $entity_manager = EntityManager::where('user_id', $user->id)->get();
            $managers_report = [
                'id' => $user->id,
                'name' => $user->name.' '.$user->surname.' '.$user->patronymic,
                'totalWorkHours' => 0,
                'totalDoneShifts' => 0,
                'shifts' => [],
                'totalCalls' => 0,
                'totalEmergencyCases' => 0,
                'caseMissed' => 0,
                'caseShiftChange' => 0,
                'caseObjectGuardMismatch' => 0,
                'caseShiftTimeExceed' => 0,
                'caseShirtError' => 0,
                'totalErrors' => 0,
                'salary' => 0
            ];
            foreach ($entity_manager as $en) {
                $entities = Entity::where('id', $en->entity_id)->get();
                foreach ($entities as $entity) {
                    $object_report = [
                        'id' => $entity->id,
                        'name' => $entity->name,
                        'totalWorkHours' => 0,
                        'totalDoneShifts' => 0,
                        'totalCalls' => 0,
                        'totalEmergencyCases' => 0,
                        'caseMissed' => 0,
                        'caseShiftChange' => 0,
                        'caseObjectGuardMismatch' => 0,
                        'caseShiftTimeExceed' => 0,
                        'caseShirtError' => 0,
                        'totalErrors' => 0,
                        'salary' => 0
                    ];
                    foreach ($shifts as $objectId => $guardShifts) {
                        if (GuardedObject::withTrashed()->find($objectId) == null) {
                            continue;
                        }
                        if ($entity->id == GuardedObject::withTrashed()->find($objectId)->entity_id) {
                            $guardedObject = GuardedObject::withTrashed()->find($objectId);
                            $guardReport = [
                                'id' => $objectId,
                                'name' => $guardedObject->name,
                                'totalWorkHours' => 0,
                                'totalDoneShifts' => count($guardShifts),
                                'shifts' => [],
                                'totalCalls' => 0,
                                'totalEmergencyCases' => 0,
                                'caseMissed' => 0,
                                'caseShiftChange' => 0,
                                'caseObjectGuardMismatch' => 0,
                                'caseShiftTimeExceed' => 0,
                                'caseShirtError' => 0,
                                'totalErrors' => 0,
                                'salary' => 0
                            ];
                            foreach ($guardShifts as $guardShift) {
                                if ($guardShift->getWorkHours() == 0) {
                                    continue;
                                }
                                $shift = [
                                    'startTime' => Carbon::parse($guardShift->start_time)->timestamp,
                                    'endTime' => Carbon::parse($guardShift->end_time)->timestamp,
                                    'totalWorkHours' => $guardShift->getWorkHours(),
                                    'salary' => $guardShift->salary,
                                    'totalDoneShifts' => 1,
                                    'totalCalls' => 0,
                                    'totalEmergencyCases' => 0,
                                    'caseMissed' => 0,
                                    'caseShiftChange' => 0,
                                    'caseObjectGuardMismatch' => 0,
                                    'caseShiftTimeExceed' => 0,
                                    'caseShirtError' => 0,
                                    'totalErrors' => 0
                                ];
                                $guardReport['totalWorkHours'] += $shift['totalWorkHours'];
                                $guardReport['salary'] += $shift['salary'];
                                $eventCounts = $guardShift->shiftEventCounts();
                                foreach ($eventCounts as $type => $count) {
                                    switch ($type) {
                                        case('checkPassed'):
                                            // no break
                                        case('customCheckPassed'):
                                            $guardReport['totalCalls'] += $count;
                                            $shift['totalCalls'] += $count;
                                            break;
                                        case('1MissedCall'):
                                            // no break
                                        case('2MissedCall'):
                                            // no break
                                        case('checkFailed'):
                                            // no break
                                        case('unknownPin'):
                                            $guardReport['caseShirtError']+= $count;
                                            $shift['caseShirtError']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        case('customCheckFailed'):
                                            $guardReport['caseMissed']+= $count;
                                            $shift['caseMissed']+= $count;
                                            $guardReport['totalEmergencyCases']+= $count;
                                            $shift['totalEmergencyCases']+= $count;
                                            $guardReport['totalCalls']+= $count;
                                            $shift['totalCalls']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        case('objectGuardMismatch'):
                                            $guardReport['caseObjectGuardMismatch']+= $count;
                                            $shift['caseObjectGuardMismatch']+= $count;
                                            $guardReport['totalEmergencyCases']+= $count;
                                            $shift['totalEmergencyCases']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        case('shiftTimeExceeded'):
                                            $guardReport['caseShiftTimeExceed']+= $count;
                                            $shift['caseShiftTimeExceed']+= $count;
                                            $guardReport['totalEmergencyCases']+= $count;
                                            $shift['totalEmergencyCases']+= $count;
                                            $guardReport['totalErrors'] += $count;
                                            $shift['totalErrors'] += $count;
                                            break;
                                        default:
                                    }
                                }
                                $guardReport['shifts'][] = $shift;
                            }
                            $object_report['totalWorkHours'] += $guardReport['totalWorkHours'];
                            $object_report['totalDoneShifts'] += $guardReport['totalDoneShifts'];
                            $object_report['totalEmergencyCases'] += $guardReport['totalEmergencyCases'];
                            $object_report['caseMissed'] += $guardReport['caseMissed'];
                            $object_report['caseShiftChange'] += $guardReport['caseShiftChange'];
                            $object_report['caseObjectGuardMismatch'] += $guardReport['caseObjectGuardMismatch'];
                            $object_report['caseShiftTimeExceed'] += $guardReport['caseShiftTimeExceed'];
                            $object_report['caseShirtError'] += $guardReport['caseShirtError'];
                            $object_report['totalErrors'] += $guardReport['totalErrors'];
                            $object_report['salary'] += $guardReport['salary'];
                        }
                        $managers_report['totalWorkHours'] += $object_report['totalWorkHours'];
                        $managers_report['totalDoneShifts'] += $object_report['totalDoneShifts'];
                        $managers_report['totalEmergencyCases'] += $object_report['totalEmergencyCases'];
                        $managers_report['caseMissed'] += $object_report['caseMissed'];
                        $managers_report['caseShiftChange'] += $object_report['caseShiftChange'];
                        $managers_report['caseObjectGuardMismatch'] += $object_report['caseObjectGuardMismatch'];
                        $managers_report['caseShiftTimeExceed'] += $object_report['caseShiftTimeExceed'];
                        $managers_report['caseShirtError'] += $object_report['caseShirtError'];
                        $managers_report['totalErrors'] += $object_report['totalErrors'];
                        $managers_report['salary'] += $object_report['salary'];
                        $managers_report['shifts'][] = $object_report;
                    }
                }
                $report[] = $managers_report;
            }
        }
        return $report;
    }   
    public static function getManagersReportExcel($from,$to) {
        $report = WorkShift::getManagersReport($from, $to);
 		$color_column = [];
        $report_row = [];
        $report_row[] = [
            'Имя менеджера',
            'Название объекта',
            'Кол-во смен',
            "Рабочее время" .chr( 13 ) .chr( 10 ) ."абонента",
            "Заработная плата " .chr( 13 ) .chr( 10 ) ." абонента",
            'Количество звонков',
            'Общее количество ошибок',
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." при дозвоне",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." заступления " .chr( 13 ) .chr( 10 ) ." на смену",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." повторного ".chr( 13 ) .chr( 10 ) ."  заступления",
            "Кол-во ошибок " .chr( 13 ) .chr( 10 ) ." превышения времени"
        ];
        $count = 1;
        foreach ($report as $k => $rep) {
            $report_row[$count][] = $rep['name'];
            $report_row[$count][] = '';
            $report_row[$count][] = (count($rep['shifts']) == 0 ) ? "0" : count($rep['shifts']);
            $report_row[$count][] = $rep['totalWorkHours'].' ч.';
            $report_row[$count][] = ($rep['salary'] == 0 ) ? "0" : $rep['salary'];
            $report_row[$count][] = ($rep['totalCalls'] == 0 ) ? "0" : $rep['totalCalls'];
            $report_row[$count][] = ($rep['totalErrors'] == 0 ) ? "0" : $rep['totalErrors'];
            $report_row[$count][] = ($rep['caseMissed'] == 0 ) ? "0" : $rep['caseMissed'];
            $report_row[$count][] = ($rep['caseShirtError'] == 0 ) ? "0" : $rep['caseShirtError'];
            $report_row[$count][] = ($rep['caseObjectGuardMismatch'] == 0 ) ? "0" : $rep['caseObjectGuardMismatch'];
            $report_row[$count][] = ($rep['caseShiftTimeExceed'] == 0 ) ? "0" : $rep['caseShiftTimeExceed'];
            $color_column[] = $count + 1;
            $count++;
            // Ищем смены
            foreach ($rep['shifts'] as $key => $value) {
                $report_row[$count][] = '';
                $report_row[$count][] = $value['name'];
                $report_row[$count][] = ($value['totalDoneShifts'] == 0 ) ? "0" : $value['totalDoneShifts'];
                $report_row[$count][] = ($value['totalWorkHours'] == 0 ) ? "0" : $value['totalWorkHours'];
                $report_row[$count][] = ($value['salary'] == 0 ) ? "0" : $value['salary'];
                $report_row[$count][] = ($value['totalCalls'] == 0 ) ? "0" : $value['totalCalls'];
                $report_row[$count][] = ($value['totalErrors'] == 0 ) ? "0" : $value['totalErrors'];
                $report_row[$count][] = ($value['caseMissed'] == 0 ) ? "0" : $value['caseMissed'];;
                $report_row[$count][] = ($value['caseShirtError'] == 0 ) ? "0" : $value['caseShirtError'];
                $report_row[$count][] = ($value['caseObjectGuardMismatch'] == 0 ) ? "0" : $value['caseObjectGuardMismatch'];
                $report_row[$count][] = ($value['caseShiftTimeExceed'] == 0 ) ? "0" : $value['caseShiftTimeExceed'];
                $count++;
            }
        }
        return [$report_row,$color_column];
    }
}
