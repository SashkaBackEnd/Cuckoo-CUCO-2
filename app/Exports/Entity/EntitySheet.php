<?php

namespace App\Exports\Entity;

use App\Entity;
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

class EntitySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents
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
    private const CUSTOMER_CONTACTS = 'Контактные данные заказчика';
    private const ENTITY_SERVICE_PHONE = 'Номер служебного телефона объекта';

    /**
     * @var Entity
     */
    private $entity;

    /**
     * @var int
     */
    private static $quantityCustomers = 2;
    /**
     *
     * @var Collection
     */
    private $customers;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
        $this->customers = $entity->customers;
        self::$quantityCustomers = $this->customers->count() + 2;
    }

    public function title(): string
    {
        return "Объект №{$this->entity->id}";
    }

    public function collection()
    {
        return collect([$this->entity])->merge($this->customers);
    }

    public function headings(): array
    {
        return [
            self::ENTITY_ID,
            self::ENTITY_NAME,
            self::ADDRESS,
            self::ENTITY_SERVICE_PHONE,
            self::CUSTOMER_NAME,
            self::CUSTOMER_CONTACTS
        ];
    }

    public function map($row): array
    {

        if ($row instanceof Entity) {
            $data = [
                $row->id,
                $row->name,
                $row->address,
                $row->service_phone,
                $row->customer_name,
                $row->customer_contacts
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
        $event->sheet->styleCells('E2:F2',
            [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],

                'fill' => [
                    'fillType' => Fill::FILL_PATTERN_MEDIUMGRAY,
                    'color' => ['hex' => 'E5E4E2']
                ]
            ]
        );

        $event->sheet->styleCells('A1:F2', [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['hex' => 'FFFFFF'],
                ]
            ],
        ]);

        if (self::$quantityCustomers !== 2) {
            $event->sheet->styleCells('E2:F' . self::$quantityCustomers, [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['hex' => 'FFFFFF'],
                    ]
                ],
            ]);
        }
    }
}
