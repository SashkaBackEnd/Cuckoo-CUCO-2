<?php

namespace App\Imports\Entity;

use App\GuardedObject;
use App\WorkTimetable;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostWorktableSheet implements ToCollection, WithHeadingRow, WithValidation
{
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

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $posts = GuardedObject::query()
            ->whereIn('id', data_get($rows, '*.' . self::FAST_ID))
            ->get();

        $workHoursToInsert = [];
        foreach ($rows as $row) {
            $fast = $posts->where('id', $row[self::FAST_ID])->first();

            if (!$fast) {
                continue;
            }

            DB::transaction(function () use ($row, $fast, &$workHoursToInsert) {
                $fast->workTimetable()->delete();

                foreach (WorkTimetable::getDays() as $day) {
                    $dayRu = WorkTimetable::convertToRu($day);

                    $workTimes = $row[$dayRu];

                    if (!$workTimes) {
                        WorkTimetable::create([
                            'day' => $day,
                            'salary' => 0,
                            'guarded_objects_id' => $fast->id
                        ]);
                        continue;
                    }

                    $workTimetable = WorkTimetable::create([
                        'day' => $day,
                        'salary' => $row["$dayRu ставка"] ?? 0,
                        'guarded_objects_id' => $fast->id
                    ]);

                    $workTimes = array_filter(explode(';', $workTimes));
                    foreach ($workTimes as $workTime) {
                        $time = explode('-', trim($workTime));
                        try {
                            $from = $time[0];
                            $to = $time[1];
                        } catch (Exception $exception) {
                            throw new HttpException(404, 'Проверьте правильность указания графика работы ' . str_replace('Undefined index:', '', $exception->getMessage()));
                        }

                        $workHoursToInsert[] = [
                            'to' => Carbon::createFromTimeString($to)->toTimeString(),
                            'from' => Carbon::createFromTimeString($from)->toTimeString(),
                            'work_timetables_id' => $workTimetable->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            });
        }
        if (!empty($workHoursToInsert)) {
            DB::table('work_hours')->insert($workHoursToInsert);
        }
        DB::commit();
    }

    public function rules(): array
    {
        return [
            self::FAST_ID => [
                'required',
                'integer'
            ],
            WorkTimetable::MON_RU => [
                'nullable'
            ],
            self::MON_SALARY => [
                'nullable'
            ],
            WorkTimetable::TUE_RU => [
                'nullable'
            ],
            self::TUE_SALARY => [
                'nullable'
            ],
            WorkTimetable::WED_RU => [
                'nullable'
            ],
            self::WED_SALARY => [
                'nullable'
            ],
            WorkTimetable::THU_RU => [
                'nullable'
            ],
            self::THU_SALARY => [
                'nullable'
            ],
            WorkTimetable::FRI_RU => [
                'nullable'
            ],
            self::FRI_SALARY => [
                'nullable'
            ],
            WorkTimetable::SAT_RU => [
                'nullable'
            ],
            self::SAT_SALARY => [
                'nullable'
            ],
            WorkTimetable::SUN_RU => [
                'nullable'
            ],
            self::SUN_SALARY => [
                'nullable'
            ],
        ];
    }
}
