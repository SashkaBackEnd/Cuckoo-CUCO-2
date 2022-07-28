<?php

namespace App\Exports\Entity;

use App\WorkHours;
use App\WorkTimetable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PostWorktableSheet implements FromCollection, ShouldAutoSize, WithTitle, WithMapping, WithHeadings
{
    /**
     * @var Collection GuardedObject
     */
    private $posts;

    /**
     * Заголовки столбцов в таблице
     * @var string
     */
    private const FAST_ID = 'Номер поста';
    private const MON_SALARY = 'Пн ставка';
    private const TUE_SALARY = 'Вт ставка';
    private const WED_SALARY = 'Ср ставка';
    private const THU_SALARY = 'Чт ставка';
    private const FRI_SALARY = 'Пт ставка';
    private const SAT_SALARY = 'Сб ставка';
    private const SUN_SALARY = 'Вс ставка';

    public function __construct(Collection $posts)
    {
        $this->posts = $posts;
    }

    public function collection(): Collection
    {
        return $this->posts;
    }

    public function headings(): array
    {
        return [
            self::FAST_ID,
            WorkTimetable::MON_RU,
            self::MON_SALARY,
            WorkTimetable::TUE_RU,
            self::TUE_SALARY,
            WorkTimetable::WED_RU,
            self::WED_SALARY,
            WorkTimetable::THU_RU,
            self::THU_SALARY,
            WorkTimetable::FRI_RU,
            self::FRI_SALARY,
            WorkTimetable::SAT_RU,
            self::SAT_SALARY,
            WorkTimetable::SUN_RU,
            self::SUN_SALARY,
        ];
    }

    public function map($fast): array
    {
        $data = [
            $fast->id
        ];

        $formatHours = function (WorkHours $hour): string {
            $from = $hour->from->format("H:i");
            $to = $hour->to->format("H:i");
            return "$from-$to";
        };

        foreach (WorkTimetable::getDays() as $day) {
            $work = $fast->workTimetable->where('day', $day)->first();
            $data[] = $work ? $work->hours->map($formatHours)->implode('; ') : '';
            $data[] = $work ? $work->salary : 0;
        }

        return $data;
    }

    public function title(): string
    {
       return "Стандартный график работы";
    }
}
