<?php

namespace App\Exports\Entity;

use App\WorkHours;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PostWorktableDatesSheet implements FromCollection, ShouldAutoSize, WithTitle, WithMapping, WithHeadings
{
    /**
     * @var Collection GuardedObject
     */
    private $workTimetableDates;

    /**
     * Заголовки столбцов в таблице
     * @var string
     */
    private const FAST_ID = 'Номер поста';
    private const WORK_DAY = 'Рабочий день';
    private const WORK_TIME = 'Рабочее время';
    private const SALARY = 'Ставка';

    /**
     * @param Collection $workTimetableDates
     */
    public function __construct(Collection $workTimetableDates)
    {
        $this->workTimetableDates = $workTimetableDates;
    }

    public function headings(): array
    {
        return [
            self::FAST_ID,
            self::WORK_DAY,
            self::WORK_TIME,
            self::SALARY
        ];
    }

    public function map($workTimetableDates): array
    {
        $data = [
            $workTimetableDates->guarded_objects_id,
            $workTimetableDates->day->format("d.m.Y")
        ];

        $formatHours = function (WorkHours $hour): string {
            $from = $hour->from->format("H:i");
            $to = $hour->to->format("H:i");
            return "$from-$to";
        };

        $data[] = $workTimetableDates->hours->map($formatHours)->implode('; ');
        $data[] = $workTimetableDates->salary;

        return $data;
    }

    public function title(): string
    {
        return "Нестандартный график работы";
    }

    public function collection()
    {
        return $this->workTimetableDates;
    }
}
