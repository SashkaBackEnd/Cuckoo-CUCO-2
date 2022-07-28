<?php

namespace App\Exports\Entity;

use App\Entity;
use App\EntityCustomer;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EntitiesSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
{

    /**
     * Название столбцов в таблице
     *
     * @var string
     */
    private const ADDRESS = 'Адрес';
    private const ENTITY_ID = 'ID объекта';
    private const ENTITY_NAME = 'Название объекта';
    private const CUSTOMER_NAME = 'Название заказчика';
    private const CUSTOMER_CONTACTS = 'Контакты заказчика №1 (не об.)';
    private const CUSTOMER_FIO = 'ФИО заказчика №1 (не об.)';
    private const PHONE = 'Телефон объекта';
    private const ENTITY_SERVICE_PHONE = 'Служебный телефон объекта';
    private const CALL_FROM = 'Обзвон с';
    private const CALL_TO = 'Обзвон до';
    private const QUANTITY_CALLS = 'Количество звонков';
    private const CALL_BACK_QUANTITY = 'Количество перезвонов';
    private const MAX_DURATION_WORK = 'Максимальное время смены';

    /**
     * @var Entity
     */
    private $entities;

    /**
     * @var int
     */
    private static $quantityCustomers = 2;
    /**
     *
     * @var Collection
     */

    public function __construct(Collection $entities)
    {
        $this->entities = $entities;
    }

    public function title(): string
    {
        return "Объекты";
    }

    public function headings(): array
    {
        return [
            self::ENTITY_ID,
            self::ENTITY_NAME,
            self::ADDRESS,
            self::PHONE,
            self::ENTITY_SERVICE_PHONE,
            self::CUSTOMER_NAME,
            self::CUSTOMER_CONTACTS,
            self::CUSTOMER_FIO,
            self::CALL_FROM,
            self::CALL_TO,
            self::QUANTITY_CALLS,
            self::CALL_BACK_QUANTITY,
            self::MAX_DURATION_WORK
        ];
    }

    public function collection()
    {
        return $this->entities;
    }

    public function map($row): array
    {
        $entity_customer = EntityCustomer::where('entity_id',  $row->id)->first();
        $customer_contacts = ($entity_customer == null) ? '' : $entity_customer->contact;
        $customer_fio = ($entity_customer == null) ? '' : $entity_customer->name;
        if ($row instanceof Entity) {
            $data = [
                $row->id,
                $row->name,
                $row->address,
                $row->phone,
                $row->service_phone,
                $row->customer_name,
                $customer_contacts,
                $customer_fio,
                date('H:i:s',strtotime($row->call_from)),
                date('H:i:s',strtotime($row->call_to)),
                $row->quantity_calls,
                $row->call_back_quantity,
                $row->max_duration_work
            ];
        } else {
            $data = [
                '',
                '',
                '',
                '',
                $row->name,
                $row->contact
            ];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });

        return [
            AfterSheet::class => [self::class, 'afterSheet']
        ];
    }

    // Хардкод
    public static function afterSheet(AfterSheet $event)
    {
       
    }
}
