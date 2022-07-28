<?php

namespace App\Imports\Entity;

use App\Entity;
use App\WorkShift;
use App\GuardedObject;
use App\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\WorkTimetable;

class PostsSheet implements ToCollection, WithValidation, SkipsEmptyRows, WithHeadingRow
{
    /**
     * Заголовки столбцов в таблице
     * @var string
     */
    private const ENTITY_ID = 'Номер объекта';
    private const FAST_ID = 'Номер поста (оставить пустым при добавлении новых постов)';
    private const FAST_NAME = 'Название поста';
    private const NUMBER_PHONE = 'Номер телефона';
    private const IS_CENTRAL = 'Центральный пост';


    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $posts = GuardedObject::query()
            ->whereIn('id', data_get($rows, '*.' . self::FAST_ID))
            ->get();

        $hasCentral = false;
        $save_ids = [];
        foreach ($rows as $row) {
            if (Entity::where('id', $row[self::ENTITY_ID])->first() == null) {
                throw new HttpException(404, 'Объекта с id '.$row[self::ENTITY_ID].' не существует');   
            }
            $fast = $posts->where('id', $row[self::FAST_ID])->first();

            if (!$fast) {
                $fast = GuardedObject::create([
                    'id' => $row[self::FAST_ID],
                    'name' => $row[self::FAST_NAME],
                    'phone' => "+".$row[self::NUMBER_PHONE],
                    'entity_id' => $row[self::ENTITY_ID],
                ]);
                $days = [
                    WorkTimetable::MON => 0,
                    WorkTimetable::TUE => 0,
                    WorkTimetable::WED => 0,
                    WorkTimetable::THU => 0,
                    WorkTimetable::FRI => 0,
                    WorkTimetable::SAT => 0,
                    WorkTimetable::SUN => 0
                ];
                foreach ($days as $day => $data) {
                    WorkTimetable::create([
                        'day' => $day,
                        'salary' =>  0,
                        'guarded_objects_id' => $fast->id
                    ]);
                }
            } else {
                $fast->update([
                    'name' => $row[self::FAST_NAME],
                    'phone' => "+".$row[self::NUMBER_PHONE],
                    'entity_id' => $row[self::ENTITY_ID]
                ]);
            }

            $save_ids[] = $fast->id;

            $isCentral = mb_strtolower($row[self::IS_CENTRAL]) === 'да';

            if ($isCentral) {
                $hasCentral = true;
                Entity::where('id', $row[self::ENTITY_ID])->update([
                    'central_guarded_objects_id' => $fast->id
                ]);
            }
            if (!$hasCentral) {
                Entity::where('id', $row[self::ENTITY_ID])->update([
                    'central_guarded_objects_id' => null
                ]);
            }
        }

        GuardedObject::whereNotIn('id', $save_ids)->delete();

        WorkShift::whereNotIn('guarded_object_id', $save_ids)->delete();

        Event::whereNotIn('guarded_object_id',  $save_ids)->delete();
        

        DB::commit();
    }

    public function rules(): array
    {
        return [
            self::ENTITY_ID => 'integer|required',
            self::FAST_ID => 'integer|nullable',
            self::FAST_NAME => 'required',
            self::NUMBER_PHONE => 'required|integer|regex:/^7[0-9]{10}$/i',
            self::IS_CENTRAL => 'required|string|min:2|max:3',
        ];
    }
}
