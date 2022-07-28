<?php

namespace App\Imports\Entity;

use App\Entity;
use App\GuardedObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PostSheet implements ToCollection, WithValidation, SkipsEmptyRows, WithHeadingRow
{
    /**
     * Заголовки столбцов в таблице
     * @var string
     */
    private const FAST_ID = 'Номер поста';
    private const FAST_NAME = 'Название поста';
    private const NUMBER_PHONE = 'Номер телефона';
    private const IS_CENTRAL = 'Центральный пост';

    /**
     * @var Entity
     */
    private $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $posts = GuardedObject::query()
            ->whereIn('id', data_get($rows, '*.' . self::FAST_ID))
            ->get();

        $hasCentral = false;
        foreach ($rows as $row) {
            $fast = $posts->where('id', $row[self::FAST_ID])->first();

            if (!$fast) {
                $fast = GuardedObject::create([
                    'id' => $row[self::FAST_ID],
                    'name' => $row[self::FAST_NAME],
                    'phone' => $row[self::NUMBER_PHONE],
                    'entity_id' => $this->entity->id,
                ]);
            } else {
                $fast->update([
                    'name' => $row[self::FAST_NAME],
                    'phone' => $row[self::NUMBER_PHONE],
                    'entity_id' => $this->entity->id
                ]);
            }

            $isCentral = mb_strtolower($row[self::IS_CENTRAL]) === 'да';

            if ($isCentral) {
                $hasCentral = true;
                $this->entity->update([
                    'central_guarded_objects_id' => $fast->id
                ]);
            }
        }

        if (!$hasCentral) {
            $this->entity->update([
                'central_guarded_objects_id' => null
            ]);
        }
        DB::commit();
    }

    public function rules(): array
    {
        return [
            self::FAST_ID => 'integer|required',
            self::FAST_NAME => 'required',
            self::NUMBER_PHONE => 'required|integer|regex:/^7[0-9]{10}$/i',
            self::IS_CENTRAL => 'required|string|min:2|max:3',
        ];
    }
}
