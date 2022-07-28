<?php

namespace App\Http\Controllers;

use App\ActionLog;
use App\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    protected $customRules = [
        'callsPerDay' => 'required|integer|min:0|max:1000',
        'dialingStatus' => 'required|integer|min:0|max:1',
        'shiftChangeTime' => 'required|integer|min:5|max:360',
        'numberOfCallAttempts' => 'required|integer|min:0|max:10',
        'reportEmail' => 'required|email',
        'maximumShiftTime' => 'required|integer|min:1|max:100',
        'phone' => 'required|regex:/^\+7[0-9]{10}$/i',
        'phone2' => 'required|regex:/^\+7[0-9]{10}$/i',
        'phone3' => 'required|regex:/^\+7[0-9]{10}$/i',
    ];

    protected $errorMessages = [
        'shiftChangeTime.min' => 'Минимально допустимое время - 5 минут',
        'shiftChangeTime.max' => 'Максимально допустимое время - 360 минут',
        'shiftChangeTime.integer' => 'Время пересменки должно быть целым числом',
        'dialingStatus.min' => 'Допустимые значения 0 и 1',
        'dialingStatus.max' => 'Допустимые значения 0 и 1',
        'dialingStatus.integer' => 'Допустимые значения 0 и 1',
        'callsPerDay.min' => 'Минимально допустимое количество звонков - 0',
        'callsPerDay.max' => 'Максимально допустимое количество звонков - 1000',
        'callsPerDay.integer' => 'Количество звонков должно быть целым числом',
        'numberOfCallAttempts.min' => 'Минимально допустимое количество попыток - 0',
        'numberOfCallAttempts.max' => 'Максимально допустимое количество попыток - 10',
        'numberOfCallAttempts.integer' => 'Количество попыток должно быть целым числом',
        'maximumShiftTime.min' => 'Минимально допустимое ограничение длительности смены - 1 час',
        'maximumShiftTime.max' => 'Максимально допустимое ограничение длительности смены - 100 часов',
        'maximumShiftTime.integer' => 'Ограничение длительности смены должно быть целым числом',
        'required' => 'Поле :attribute обязательно для заполнения',
    ];

    protected $customAttributeNames = [
        'dialingStatus' => 'Статус автообзвона',
        'callsPerDay' => 'Число звонков в сутки',
        'shiftChangeTime' => 'Время пересменки',
        'numberOfCallAttempts' => 'Количество попыток перезвонить',
        'reportEmail' => 'Почта для отчетов',
        'maximumShiftTime' => 'Ограничение длительности смены',
        'phone' => 'Телефон',
        'phone2' => 'Телефон',
        'phone3' => 'Телефон',
    ];

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
        $result = [
            'dialingStatus' => intval($settings['dialing_status']),
            'callsPerDay' => intval($settings['calls_per_day']),
            'shiftChangeTime' => intval($settings['shift_change_time']),
            'numberOfCallAttempts' => intval($settings['number_of_call_attempts']),
            'reportEmail' => $settings['report_email'],
            'maximumShiftTime' => intval($settings['maximum_shift_time']),
            'phone' => $settings['phone'],
            'phone2' => $settings['phone_2'],
            'phone3' => $settings['phone_3'],
        ];
        return response($result, 200);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->customRules, $this->errorMessages, $this->customAttributeNames);
        $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
        $needRegenerateDialQueue = false;
        if ($settings['calls_per_day'] != $validatedData['callsPerDay']) {
            $needRegenerateDialQueue = true;
            ActionLog::addToLog(sprintf('Изменил количество звонков в день с %d на %d', $settings['calls_per_day'], $validatedData['callsPerDay']));
        }
        if ($settings['dialing_status'] != $validatedData['dialingStatus']) {
            if ($validatedData['dialingStatus'] == 1) {
                ActionLog::addToLog('Включил глобальный обзвон');
            } else {
                ActionLog::addToLog('Выключил глобальный обзвон');
            }
        }
        if ($settings['shift_change_time'] != $validatedData['shiftChangeTime']) {
            ActionLog::addToLog(sprintf('Изменил время на пересменку с %d на %d', $settings['shift_change_time'], $validatedData['shiftChangeTime']));
        }
        if ($settings['number_of_call_attempts'] != $validatedData['numberOfCallAttempts']) {
            ActionLog::addToLog(sprintf('Изменил число попыток дозвона с %d на %d', $settings['number_of_call_attempts'], $validatedData['numberOfCallAttempts']));
        }
        if ($settings['report_email'] != $validatedData['reportEmail']) {
            ActionLog::addToLog(sprintf('Изменил почту для отчетов с %s на %s', $settings['report_email'], $validatedData['reportEmail']));
        }
        if ($settings['maximum_shift_time'] != $validatedData['maximumShiftTime']) {
            ActionLog::addToLog(sprintf('Изменил ограничение длительности смены с %d на %d', $settings['maximum_shift_time'], $validatedData['maximumShiftTime']));
        }
        if ($settings['phone'] != $validatedData['phone']) {
            ActionLog::addToLog(sprintf('Изменил номер телефона руководителя с %s на %s', $settings['phone'], $validatedData['phone']));
        }
        if ($settings['phone'] != $validatedData['phone2']) {
            ActionLog::addToLog(sprintf('Изменил номер телефона руководителя с %s на %s', $settings['phone_2'], $validatedData['phone2']));
        }
        if ($settings['phone'] != $validatedData['phone3']) {
            ActionLog::addToLog(sprintf('Изменил номер телефона руководителя с %s на %s', $settings['phone_3'], $validatedData['phone3']));
        }
        DB::table('global_settings')->where('name', 'dialing_status')->update(['value' => $validatedData['dialingStatus']]);
        DB::table('global_settings')->where('name', 'calls_per_day')->update(['value' => $validatedData['callsPerDay']]);
        DB::table('global_settings')->where('name', 'shift_change_time')->update(['value' => $validatedData['shiftChangeTime']]);
        DB::table('global_settings')->where('name', 'number_of_call_attempts')->update(['value' => $validatedData['numberOfCallAttempts']]);
        DB::table('global_settings')->where('name', 'report_email')->update(['value' => $validatedData['reportEmail']]);
        DB::table('global_settings')->where('name', 'maximum_shift_time')->update(['value' => $validatedData['maximumShiftTime']]);
        DB::table('global_settings')->where('name', 'phone')->update(['value' => $validatedData['phone']]);
        DB::table('global_settings')->where('name', 'phone_2')->update(['value' => $validatedData['phone2']]);
        DB::table('global_settings')->where('name', 'phone_3')->update(['value' => $validatedData['phone3']]);

        $settings = DB::table('global_settings')->select('name', 'value')->get()->pluck('value', 'name');
        $result = [
            'dialingStatus' => intval($settings['dialing_status']),
            'callsPerDay' => intval($settings['calls_per_day']),
            'shiftChangeTime' => intval($settings['shift_change_time']),
            'numberOfCallAttempts' => intval($settings['number_of_call_attempts']),
            'reportEmail' => $settings['report_email'],
            'maximumShiftTime' => intval($settings['maximum_shift_time']),
            'phone' => $settings['phone'],
            'phone2' => $settings['phone_2'],
            'phone3' => $settings['phone_3'],
        ];
        if ($needRegenerateDialQueue) {
            // TODO изменил на всякий случай, но скорее всего глобальные настройки не будут использоваться
            DB::transaction(function () {
                DB::table('queued_calls')->where('call_status', 'queued')->delete();
                DB::table('guarded_objects')->update(['queue_end_time' => time()]);
                Entity::all()->each(function ($entity) {
                    $entity->generateCallQueue();
                });
            });
        }
        return response($result, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
