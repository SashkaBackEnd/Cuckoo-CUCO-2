<?php

namespace App\Imports\SecurityGuard;

use App\SecurityGuard;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecurityGuardImport implements ToCollection, WithValidation, SkipsEmptyRows, WithHeadingRow
{
    /**
     * Название столбцов в таблице
     * @var string
     */
    private const PIN = 'PIN';
    private const NAME = 'Имя';
    private const CAR = 'Машина';
    private const GUN = 'Оружие';
    private const DEBTS = 'Долги';
    private const LICENSE = 'УЛЧО';
    private const STATUS = 'Статус';
    private const SURNAME = 'Фамилия';
    private const COMMENT = 'Комментарий';
    private const PATRONYMIC = 'Отчество';
    private const PHONE = 'Номер телефона';
    private const WORK_TYPE = 'Тип работы';
    private const LICENSE_RANK = 'УЛЧО ранг';
    private const BIRTH_DATE = 'Дата рождения';
    private const KNEW_ABOUT_US = 'Узнал о нас';
    private const LEFT_THINGS = 'Оставленные вещи';
    private const LICENSE_TO_DATE = 'Срок лицензии';
    private const MEDICAL_BOOK = 'Медицинская книжка';
    private const DRIVING_LICENSE = 'Водительские права';

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $guards = SecurityGuard::query()
            ->whereIn('pin', data_get($rows, '*.' . self::PIN))
            ->get();

        foreach ($rows as $row) {
            $guard = $guards->where('pin', $row[self::PIN])->first();
            $license = false;
            if($row[self::LICENSE] === 'да') {
                $license = true;
            }
            $driving_license = false;
            if($row[self::DRIVING_LICENSE] === 'да') {
                $driving_license = true;
            }

            // throw new HttpException(404, Carbon::createFromDate($row[self::BIRTH_DATE])->toDateString());
            $data = [
                'surname' => $row[self::SURNAME],
                'name' => $row[self::NAME],
                'patronymic' => $row[self::PATRONYMIC],
                // 'birth_date' => Carbon::createFromDate($row[self::BIRTH_DATE])->toDateString(),
                'birth_date' => Carbon::parse(strtotime($row[self::BIRTH_DATE]))->format('Y-m-d'),
                'phone' => "+{$row[self::PHONE]}",
                'license' => $license,
                'license_to_date' => $row[self::LICENSE_TO_DATE]
                    ? Carbon::createFromDate($row[self::LICENSE_TO_DATE])->toDateString()
                    : null,
                'comment' => $row[self::COMMENT],
                'left_things' => $row[self::LEFT_THINGS],
                'driving_license' => $driving_license,
                'car' => $row[self::CAR],
                'medical_book' => $row[self::MEDICAL_BOOK],
                'gun' => $row[self::GUN],
                'debts' => $row[self::DEBTS],
                'work_type' => $row[self::WORK_TYPE],
                'license_rank' => $row[self::LICENSE_RANK],
                'knew_about_us' => $row[self::KNEW_ABOUT_US]
            ];

            if ($guard) {
                $guard->update($data);
            } else {
                $securityGuardId = SecurityGuard::getNextId();
                $data['pin'] = SecurityGuard::calculatePin($securityGuardId);
                DB::table('security_guards')->insert($data);
            }
        }
    }

    public function rules(): array
    {
        return [
            self::PIN => 'integer|nullable',
            self::NAME => 'required',
            self::CAR => 'nullable',
            self::GUN => 'nullable',
            self::DEBTS => 'nullable',
            self::LICENSE => ['string','nullable'],
            self::STATUS => ['required', 'string', 'regex:/(^обычный|^служебный)/i'],// todo вынести как константы
            self::LICENSE_TO_DATE => 'nullable',
            self::SURNAME => 'required',
            self::COMMENT => 'nullable',
            self::PHONE => ['required', 'integer', 'regex:/^7[0-9]{10}$/i'],
            self::WORK_TYPE => ['required', 'string', 'regex:/(^смены|^вахта)/i'], // todo вынести как константы
            self::LICENSE_RANK => 'nullable|integer|between:1,9',
            self::BIRTH_DATE => 'required',
            self::KNEW_ABOUT_US => 'nullable',
            self::LEFT_THINGS => 'nullable',
            self::MEDICAL_BOOK => 'nullable',
            self::DRIVING_LICENSE => ['string', 'nullable']
        ];
    }
}
