<?php
/**
 * Created by PhpStorm.
 * User: Гагарин
 * Date: 10.02.2020
 * Time: 16:47
 */

namespace App;


class Zvonok
{
    protected $publicKey;
    protected $campaignId;
    protected $incomeCampaignId;

    public function __construct()
    {
        $this->publicKey = env('ZVONOK_KEY');
        $this->campaignId = env('ZVONOK_CAMPAIGN_ID');
        $this->incomeCampaignId = env('ZVONOK_INCOME_CAMPAIGN_ID');
    }

    public function addCall($phone)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://calltools.ru/lk/cabapi_external/api/v1/phones/call/");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "public_key=" . $this->publicKey . "&phone=" . $phone . "&campaign_id=" . $this->campaignId . "&text=<break time=\"1200ms\"/> Здравствуйте. Введите пинкод");
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    public function getCallInfo($callId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://calltools.ru/lk/cabapi_external/api/v1/phones/call_by_id/?public_key=" . $this->publicKey . "&call_id=" . $callId);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    public function getIncomeCalls()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://calltools.ru/lk/cabapi_external/api/v1/phones/all_calls/?public_key=" . $this->publicKey . "&campaign_id=" . $this->incomeCampaignId);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}