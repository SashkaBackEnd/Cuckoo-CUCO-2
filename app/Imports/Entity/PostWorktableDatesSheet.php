<?php

namespace App\Imports\Entity;

use App\GuardedObject;
use App\WorkTimetableDates;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostWorktableDatesSheet implements ToCollection, WithHeadingRow, WithValidation
{

    /**
     * Заголовки столбцов в таблице
     * @var string
     */
    private const FAST_ID = 'Номер поста';
    private const WORK_DAY = 'Рабочий день';
    private const WORK_TIME = 'Рабочее время';
    private const SALARY = 'Ставка';

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
                $day = Carbon::createFromDate($row[self::WORK_DAY]);

                $workTimetableDates = WorkTimetableDates::query()
                    ->where('day', $day->toDateString())
                    ->where('guarded_objects_id', $fast->id)
                    ->first();

                if (!$workTimetableDates) {
                    $workTimetableDates = WorkTimetableDates::create([
                        'day' => $day->toDateString(),
                        'salary' => $row[self::SALARY] ?? 0,
                        'guarded_objects_id' => $fast->id
                    ]);
                }

                $workTimes = array_filter(explode(';', $row[self::WORK_TIME]));
                foreach ($workTimes as $time) {
                    $time = explode('-', trim($time));
                    try {
                        $from = $time[0];
                        $to = $time[1];
                    } catch (Exception $exception) {
                        throw new HttpException(404, 'Проверьте правильность указания графика работы ' . str_replace('Undefined index:', '', $exception->getMessage()));
                    }

                    $workHoursToInsert[] = [
                        'to' => Carbon::createFromTimeString($to)->toTimeString(),
                        'from' => Carbon::createFromTimeString($from)->toTimeString(),
                        'work_timetables_date_id' => $workTimetableDates->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            });
        }
        if (!empty($workHoursToInsert)) {
            DB::table('work_hours')->insert($workHoursToInsert);
        }
    }

    public function rules(): array
    {
        return [
            self::FAST_ID => [
                'required'
            ],
            self::WORK_DAY => [
                'required'
            ],
            self::WORK_TIME => [
                'required'
            ],
            self::SALARY => [
                'nullable',
                'integer'
            ]];
    }
}
