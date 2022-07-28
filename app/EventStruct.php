<?php

namespace App;

abstract class EventStruct
{
    /**
     * Кол-во звонков
     * @var int
     */
    public $callCount = 0;

    /**
     * Кол-во ошибок
     * @var int
     */
    public $errorsCount = 0;

    /**
     * Кол-во ошибок при дозвоне системы
     * @var int
     */
    public $callInErrors = 0;

    /**
     * Кол-во ошибок при заступлении на смену
     * @var int
     */
    public $startShiftErrors = 0;

    /**
     * Кол-во ошибок при повторном заступлении на смену
     * @var int
     */
    public $reStartShiftErrors = 0;

    /**
     * Кол-во ошибок при превышении смены
     * @var int
     */
    public $overtimeShiftErrors = 0;
}
