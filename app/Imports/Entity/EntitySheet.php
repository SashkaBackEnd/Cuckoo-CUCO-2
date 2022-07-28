<?php

namespace App\Imports\Entity;

use App\Entity;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntitySheet implements ToCollection, WithHeadingRow
{
    use RemembersRowNumber;

    /**
     * @var Entity
     */
    private $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

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
     *
     * @param int $number
     * @return int|null
     */
    public function saveAndGetNumberRow(int $number): ?int
    {
        $this->rememberRowNumber($number);
        return $this->getRowNumber();
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $entityCustomersForCreate = [];
        foreach ($rows as $key => $row) {
            $numberRow = $this->saveAndGetNumberRow($key);

            $content = $this->handleHeadingRows($row);

            if ($numberRow !== 0) {
                if ($content['customer_name'] === ' ' ||
                    $content['customer_contacts'] === ' ') {
                    continue;
                }

                $entityCustomersForCreate[] = [
                    'name' => $content['customer_name'],
                    'contact' => $content['customer_contacts'],
                    'entity_id' => $this->entity->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                continue;
            }

            if (!$content['id'] ||
                $content['address'] === ' ' ||
                $content['name'] === ' ' ||
                $content['service_phone'] === ' ') {
                throw new HttpException(404, 'Значения объекта не должны быть пустыми');
            }

            $idIsBusy = DB::table('entities')
                ->where('id', '<>', $this->entity->id)
                ->where('id', $content['id'])
                ->exists();

            if ($idIsBusy) {
                throw new HttpException(404, 'Данный код объекта занят');
            }

            $this->entity->update([
                'id' => $content['id'],
                'address' => $content['address'],
                'name' => $content['name'],
                'service_phone' => $content['service_phone']
            ]);
        }

        DB::transaction(function () use ($entityCustomersForCreate) {
            $this->entity->customers()->delete();
            DB::table('entity_customers')->insert($entityCustomersForCreate);
        });
        DB::commit();
    }

    public function handleHeadingRows(Collection $row): array
    {
        try {
            $data = [
                'id' => $row[self::ENTITY_ID],
                'address' => $row[self::ADDRESS],
                'name' => $row[self::ENTITY_NAME],
                'service_phone' => $row[self::ENTITY_SERVICE_PHONE],
                'customer_name' => $row[self::CUSTOMER_NAME],
                'customer_contacts' => $row[self::CUSTOMER_CONTACTS]
            ];
        } catch (Exception $exception) {
            throw new HttpException(404, 'Отсутствует столбец: ' . str_replace('Undefined index:', '', $exception->getMessage()));
        }
        return $data;
    }
}
