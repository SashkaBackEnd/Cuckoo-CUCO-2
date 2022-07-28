<?php

namespace App\Exports\Entity;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PostSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
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
    private const FAST_NAME = 'Название поста';
    private const NUMBER_PHONE = 'Номер телефона';
    private const IS_CENTRAL = 'Центральный пост';

    public function __construct(Collection $posts)
    {
        $this->posts = $posts;
    }

    public function collection(): Collection
    {
        return $this->posts;
    }

    public function title(): string
    {
        return "Посты";
    }

    public function headings(): array
    {
        return [
            self::FAST_ID,
            self::FAST_NAME,
            self::NUMBER_PHONE,
            self::IS_CENTRAL,
        ];
    }

    public function map($fast): array
    {
        $isFastCentral = $fast->entity->central_guarded_objects_id === $fast->id;
        return [
            $fast->id,
            $fast->name,
            $fast->phone,
            $isFastCentral ? 'Да' : 'Нет',
        ];
    }
}
