<?php

namespace App\Exports\SecurityGuard;

use App\SecurityGuard;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SecurityGuardExport implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings
{
    use Exportable;

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

    public function collection()
    {
        return SecurityGuard::all();
    }

    public function headings(): array
    {
        return [
            self::PIN,
            self::NAME,
            self::SURNAME,
            self::PATRONYMIC,
            self::BIRTH_DATE,
            self::STATUS,
            self::PHONE,
            self::WORK_TYPE,
            self::LICENSE,
            self::LICENSE_RANK,
            self::LICENSE_TO_DATE,
            self::GUN,
            self::DEBTS,
            self::LEFT_THINGS,
            self::MEDICAL_BOOK,
            self::DRIVING_LICENSE,
            self::CAR,
            self::KNEW_ABOUT_US,
            self::COMMENT
        ];
    }

    public function map($securityGuard): array
    {
        return [
            $securityGuard->pin,
            $securityGuard->name,
            $securityGuard->surname,
            $securityGuard->patronymic,
            Carbon::createFromDate($securityGuard->birth_date)->format('d.m.Y'),
            $securityGuard->status,
            $securityGuard->phone,
            $securityGuard->work_type,
            $securityGuard->license === 1 ? 'да' : 'нет',
            $securityGuard->license_rank,
            $securityGuard->license_to_date,
            $securityGuard->gun,
            $securityGuard->debts,
            $securityGuard->left_things,
            $securityGuard->medical_book,
            $securityGuard->driving_license === 1 ? 'да' : 'нет',
            (string)$securityGuard->car,
            $securityGuard->knew_about_us,
            $securityGuard->comment
        ];
    }
}
