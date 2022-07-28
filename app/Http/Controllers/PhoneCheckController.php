<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\AsterDialer;
use App\GuardedObject;
use App\QueuedCall;

class PhoneCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function check($objectId)
    {
        $guardedObject = GuardedObject::findOrFail($objectId);
        $currentShift = $guardedObject->workShift()->where('shift_status', 'process')->first();
        if ($currentShift) {
            $queuedCallData = [
                'call_date' => time(),
                'guarded_object_id' => $guardedObject->id,
                'call_status' => 'custom',
            ];
            $queuedCall = QueuedCall::create($queuedCallData);
            $dialer = new AsterDialer();
            $dialer->dialByQueueId($queuedCall->id);
            ActionLog::addToLog(sprintf('Запустил ручную проверку поста "%s"', $guardedObject->name));
            return response($queuedCall->id, 200);
        }
        return response('На объекте нет охранника', 403);

    }
}
