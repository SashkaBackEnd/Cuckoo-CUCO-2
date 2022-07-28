<?php

namespace App;


class Telegram
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('TELEGRAM_API_KEY');
        $this->apiUrl = 'https://api.telegram.org/bot';
    }

    public static function sendMessage($chatId, $text)
    {
        $data = [
            'bot_token' => env('TELEGRAM_API_KEY'),
            'method' => 'sendMessage',
            'args' => json_encode([
                'chat_id' => $chatId,
                'text' => $text,
                ]),
        ];

        $url = env('TELEGRAM_PROXY_SCRIPT_URL');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_exec($curl);
        curl_close($curl);
    }
}