<?php


namespace App\SMS;


use Illuminate\Support\Facades\Storage;
use stdClass;

class SMS
{
    /**
     * Клиент СМС-РУ
     * @var SMSRUClient
     */
    private $smsRu;

    public function __construct()
    {
        $this->smsRu = new SMSRUClient("2249B5BA-0252-E84F-4F80-09F97D0C880A");
    }

    /**
     * Отправить смс
     * @param string $to телефон на который происходит отправление
     * @param string $text текст смс
     * @param string $from Если у вас уже одобрен буквенный отправитель, его можно указать здесь,
     * в противном случае будет использоваться ваш отправитель по умолчанию
     * @param int $time Отложить отправку
     * @param int $translit Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
     * @param string $partnerId можно указать ваш ID партнера, если вы интегрируете код в чужую систему
     */
    public function send(string $to, string $text, string $from = '', int $time = 0, int $translit = 0, string $partnerId = ''): stdClass
    {
        $data = new stdClass();
        $data->to = $to;
        $data->text = $text;
        $data->from = $from;
        $data->time = time() + $time;
        $data->translit = $translit;
        $data->partner_id = $partnerId;

        $sms = $this->smsRu->send_one($data); // Отправка сообщения

        if ($sms->status !== "OK") { // Запрос выполненный с ошибкой
            Storage::disk('logs')->prepend('sms_ru/sms_error.log', now()->toDateTimeString() . ": " . $sms->status);
        }
        
        return $sms;
    }
}
