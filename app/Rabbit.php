<?php

namespace App;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rabbit
{
    protected $connection;
    protected $channel;

    /**
     * Rabbit constructor.
     * @param array $queueNames массив названий очередей, в которые будут отправляться сообщения
     */
    public function __construct($queueNames = ['socket'])
    {
        // Создаем подключение к RabbitMQ-серверу
        $this->connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USER'), env('RABBITMQ_PASSWORD'));
        // Получаем канал соединения
        $this->channel = $this->connection->channel();
        // Объявляем очереди
        foreach ($queueNames as $name) {
            $this->channel->queue_declare($name, false, false, false, false);
        }
    }

    public function __destruct()
    {
        // Закрываем  канал и соединение
        $this->channel->close();
        try {
            $this->connection->close();
        } catch (\Exception $e) {
            //TODO Добавить логирование ошибки
        }
    }

    /** Отправляет json-массив с переданными параметрами на сервер RabbitMQ
     * @param $text string Текст сообщения
     */
    public function sendForSocket($text)
    {
        $msg = new AMQPMessage($text);
        $this->channel->basic_publish($msg, '', env('RABBITMQ_QUEUE'));
    }
}