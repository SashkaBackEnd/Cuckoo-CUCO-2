<?php

namespace App\Imports\Entity;

use App\Entity;
use App\EntityCustomer;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntitiesSheet implements ToCollection, WithHeadingRow
{
    use RemembersRowNumber;

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

            if (!$content['id'] ||
                $content['address'] == null ||
                $content['name'] == null ||
                $content['phone'] == null ||
                $content['customer_name'] == null ||
                $content['call_from'] == null ||
                $content['call_to'] == null ||
                $content['quantity_calls'] == null ||
                $content['call_back_quantity'] == null ||
                $content['max_duration_work'] == null ||
                $content['service_phone'] == null) {
                // throw new HttpException(404, 'Значения объекта не должны быть пустыми');
            }
            if (Entity::where('id', '!=', $content['id'])->where('phone', $content['phone'])->first()) {
                throw new HttpException(404, 'Телефон занят другим объектом');
            }
            if (Entity::where('id', '!=', $content['id'])->where('service_phone', $content['service_phone'])->first()) {
                throw new HttpException(404, 'Телефон занят другим объектом');
            }
            if (Entity::where('id', $content['id'])->first() != null) {
                Entity::where('id', $content['id'])->update([
                    'id' => $content['id'],
                    'address' => $content['address'],
                    'name' => $content['name'],
                    'service_phone' => $content['service_phone'],
                    'customer_name' => $content['customer_name'],
                    'phone' => $content['phone'],
                    'call_from' => $content['call_from'],
                    'call_to' => $content['call_to'],
                    'quantity_calls' => $content['quantity_calls'] = null ? 0 : $content['quantity_calls'],
                    'call_back_quantity' => $content['call_back_quantity'] = null ? 0 : $content['call_back_quantity'],
                    'max_duration_work' => $content['max_duration_work'] = null ? 0 : $content['max_duration_work']
                ]);
            } else {
               Entity::create([
                    'id' => $content['id'],
                    'address' => $content['address'],
                    'name' => $content['name'],
                    'service_phone' => $content['service_phone'],
                    'customer_name' => $content['customer_name'],
                    'phone' => $content['phone'],
                    'call_from' => $content['call_from'],
                    'call_to' => $content['call_to'],
                    'quantity_calls' => $content['quantity_calls'] = null ? 0 : $content['quantity_calls'],
                    'call_back_quantity' => $content['call_back_quantity'] = null ? 0 : $content['call_back_quantity'],
                    'max_duration_work' => $content['max_duration_work'] = null ? 0 : $content['max_duration_work']
                ]); 
            }
            if ($content['customer_fio'] != null && $content['customer_contacts'] != null) {
                $entity_customer = EntityCustomer::where('entity_id', $content['id'])->first();
                if ($entity_customer == null) {
                    EntityCustomer::create([
                        'name' => $content['customer_fio'],
                        'contact' => $content['customer_contacts'],
                        'entity_id' => $content['id']
                    ]);
                } else {
                    EntityCustomer::where('entity_id', $content['id'])->first()->update([
                        'name' => $content['customer_fio'],
                        'contact' => $content['customer_contacts']
                    ]);
                }
            }
        }
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
                'customer_contacts' => $row[self::CUSTOMER_CONTACTS],
                'customer_fio' => $row[self::CUSTOMER_FIO],
                'phone' => $row[self::PHONE],
                'call_from' => date('Y-m-d H:i:s',strtotime($row[self::CALL_FROM])),
                'call_to' => date('Y-m-d H:i:s',strtotime($row[self::CALL_TO])),
                'quantity_calls' => $row[self::QUANTITY_CALLS],
                'call_back_quantity' => $row[self::CALL_BACK_QUANTITY],
                'max_duration_work' => $row[self::MAX_DURATION_WORK]
            ];
        } catch (Exception $exception) {
            throw new HttpException(404, 'Отсутствует столбец: ' . str_replace('Undefined index:', '', $exception->getMessage()));
        }
        return $data;
    }
}
