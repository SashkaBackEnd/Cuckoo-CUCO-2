<?php

namespace App;


use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\Action\OriginateAction;

class AsterDialer
{
    public $pamiClientOptions;

    public function __construct()
    {
        $this->pamiClientOptions = [
            'host' => '127.0.0.1',
            'scheme' => 'tcp://',
            'port' => '5038',
            'username' => 'cuckoo',
            'secret' => 'UYkeA6ji',
            'connect_timeout' => 10000,
            'read_timeout' => 10000
        ];
    }

    // TODO здесь делать проверку на то, с какого номера осуществлять звонок

    //TODO в вызывающем методе уже осуществляется поиск QueuedCall.
    // Проверить где еще используется метод dialByQueueId.
    // Изменить входной параметр $queuedCallId на модель QueuedCall, т.к. здесь проблема n+1.
    // в методе dialQueue вызов данного метода осуществляется в цикле

    //Todo метод используется в разных частях приложения, необходим рефакторинг

    public function dialByQueueId($queuedCallId)
    {
        $queuedCall = QueuedCall::with('guardedObject.entity')->find($queuedCallId);
        if (is_null($queuedCall)) {
            return;
        }

        if ($queuedCall->call_status != 'queued' && $queuedCall->call_status != 'custom' && $queuedCall->call_status != 'callback') {
            return;
        }

        // TODO не нашел где используется данный статус globalpause
        //  Удалил присвоение статуса globalpause
        //  PS смотреть в гите
        $guardedObject = $queuedCall->guardedObject;
        if (is_null($guardedObject)) {
            return;
        }

        $entity = $guardedObject->entity;
        if (is_null($entity)) {
            return;
        }

        
        if ($entity->dialing_status == 0 && $queuedCall->call_status != 'custom'  && $queuedCall->call_status != 'callback') {
            $queuedCall->call_status = 'objectpause';
            $queuedCall->save();
            return;
        }

        if (!$entity->hasCentralPost() && count($guardedObject->currentShifts()) === 0 && $queuedCall->call_status != 'callback') {
            $queuedCall->save();
            return;
        }

        if (count($guardedObject->currentShifts()) === 0 && $queuedCall->call_status == 'queued') {
            $queuedCall->save();
            return;
        }

        $isCustom = $queuedCall->call_status == 'custom';
        $isCallback = $queuedCall->call_status == 'callback';
        if ($isCallback) {
        	$queuedCall->call_status = 'process_callback';
        } else {
        	$queuedCall->call_status = 'process';
        }
        $queuedCall->save();

        $pamiClient = new ClientImpl($this->pamiClientOptions);
        $objectPhone = trim($guardedObject->phone, '+');
        $pamiClient->open();
        $originate = new OriginateAction('SIP/' . $objectPhone . '@rtk');

        $originate->setCallerId($objectPhone);
        $originate->setContext('pinout');
        $originate->setExtension('s');
        $originate->setTimeout(35000);
        $originate->setPriority(1);
        $originate->setAsync(true);
        if ($isCustom) {
            $originate->setVariable('CDR(direction)', 'outcustom');
        } else {
            $originate->setVariable('CDR(direction)', 'out');
        }
        $originate->setVariable('CDR(guarded_object_id)', $queuedCall->guarded_object_id);
        $originate->setVariable('CDR(queued_call_id)', $queuedCallId);

        $message = $pamiClient->send($originate);
        $pamiClient->close();
    }

    public function dialCustomCheck($guardedObjectId)
    {
        $guardedObject = GuardedObject::find($guardedObjectId);
        if (is_null($guardedObject)) {
            return;
        }
        if (count($guardedObject->currentShifts()) == 0) {
            return;
        }

        $pamiClient = new ClientImpl($this->pamiClientOptions);
        $objectPhone = trim($guardedObject->phone, '+');
        $pamiClient->open();
        $originate = new OriginateAction('SIP/' . $objectPhone . '@rtk');

        $originate->setCallerId($objectPhone);
        $originate->setContext('pinout');
        $originate->setExtension('s');
        $originate->setTimeout(35000);
        $originate->setPriority(1);
        $originate->setAsync(true);
        $originate->setVariable('CDR(direction)', 'outcustom');
        $originate->setVariable('CDR(guarded_object_id)', $guardedObjectId);

        $message = $pamiClient->send($originate);
        $pamiClient->close();
    }

    public static function dialQueue()
    {
        $asterDialer = new AsterDialer();

        $queuedCalls = QueuedCall::where('call_date', '<=', time())
            ->where(function($query) {
              return $query->where('call_status', '=', 'queued')
                ->orWhere('call_status', '=', 'custom')->orWhere('call_status', '=', 'callback');
            })
            ->get();
        foreach ($queuedCalls as $queuedCall) {
            $asterDialer->dialByQueueId($queuedCall->id);
        }
    }
}
