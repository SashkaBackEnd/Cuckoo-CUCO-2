<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\Event;
use App\GuardedObject;
use App\Rabbit;
use App\SecurityGuard;
use App\WorkShift;
use App\WorkTimetable;
use App\WorkTimetableDates;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function startShift(Request $request)
    {
        $validatedData = $request->validate([
            'objectId' => 'required|integer|exists:App\GuardedObject,id',
            'guardId' => 'required|integer|exists:App\SecurityGuard,id',
        ]);
        $guardedObject = GuardedObject::find($validatedData['objectId']);
        if (is_null($guardedObject)) {
            return response('Объекта нет или он был удален', 403);
        }


        $securityGuard = SecurityGuard::find($validatedData['guardId']);
    


        if (is_null($securityGuard)) {
            return response('Охранника нет или он был удален', 403);
        }
        $guardShift = WorkShift::getCurrentShiftForGuard($validatedData['guardId']);
        if ($guardShift) {
            return response('Этот охранник уже работает', 403);
        }
        $data = [
            'guarded_object_id' => $validatedData['objectId'],
            'security_guard_id' => $validatedData['guardId'],
            'shift_status' => 'process',
            'start_time' => time(),
            'end_time' => null,
        ];
        $workShift = WorkShift::create($data);

        $eventData = ['type' => 'startShift', 'security_guard_id' => $validatedData['guardId'], 'guarded_object_id' => $validatedData['objectId']];
        $event = Event::create($eventData);

        ActionLog::addToLog(sprintf('Начал смену на объекте "%s" для охранника %s', $guardedObject->name, $securityGuard->fullName), $event->id);
        
        $socketMessage = [
            'fetchObjects' => '',
            'fetchGuards' => '',
            'fetchObjectById' => $guardedObject->id,
            'fetchGuardById' => [$securityGuard->id],
        ];
        /*$rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        $guardedObject->noteIfNoGuard();

        return response($workShift->info(), 200);
    }

    public function endShift(Request $request)
    {
        $validatedData = $request->validate([
            'guardId' => 'required|integer|exists:App\SecurityGuard,id',
        ]);
        $securityGuard = SecurityGuard::find($validatedData['guardId']);
        if (is_null($securityGuard)) {
            return response('Охранника нет или он был удален', 403);
        }
        $workShift = WorkShift::getCurrentShiftForGuard($validatedData['guardId']);
        if (!$workShift) {
            return response('Этот охранник не находился на смене', 403);
        }
        $guardedObject = $workShift->guardedObject()->first();
        $workShift->shift_status = 'done';
        $workShift->end_time = time();
        $workShift->salary = $securityGuard->doneSalary();
        $workShift->save();

        // Подсчёт зарплаты
 	
   
            

        $eventData = ['type' => 'endShift', 'security_guard_id' => $validatedData['guardId'], 'guarded_object_id' => $workShift->guarded_object_id];
        $event = Event::create($eventData);
        ActionLog::addToLog(sprintf('Завершил смену на объекте "%s" для охранника %s', $guardedObject->name, $securityGuard->fullName), $event->id);
        $socketMessage = [
            'fetchObjects' => '',
            'fetchGuards' => '',
            'fetchObjectById' => $workShift->guarded_object_id,
            'fetchGuardById' => [$securityGuard->id],
        ];
        /*$rabbit = new Rabbit();
        $rabbit->sendForSocket(json_encode($socketMessage));*/
        $workShift->guardedObject()->first()->noteIfNoGuard();
        return response($workShift->info(), 200);
    }
}
