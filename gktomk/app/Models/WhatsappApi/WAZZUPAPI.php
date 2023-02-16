<?php
namespace GKTOMK\Models\WhatsappApi;

class WAZZUPAPI
{
    public static function getChannels(){
        return self::call('channels', '', 'GET');
    }

    public static function sendMessage($data = []){
        return self::call('message', $data, 'POST');
    }

    public static function call($url, $data = [], $method = 'GET')
    {

        if (!empty($data) and $method == 'GET') {
            $query = urldecode(http_build_query($data));
            $url .= '?' . $query;

            $url = str_replace('date[0]', 'date[]', $url);
            $url = str_replace('date[1]', 'date[]', $url);
        }


        $urlApi = 'https://api.wazzup24.com/v3/';
        $url = $urlApi . $url;


        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Integration by Ed-Support.RU');

        if ($method == 'GET') curl_setopt($curl, CURLOPT_HTTPGET, 1);
        elseif ($method == 'POST') curl_setopt($curl, CURLOPT_POST, 1);
        else if ($method == 'DELETE') curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");


        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);

        $headers[] = 'Authorization: Bearer 9622bc6972d044c791f98dba68c1b752';
        if (!empty($data) and ($method == 'POST' or $method == 'DELETE')) {
            $headers[] = 'Content-Type: application/json';
            $query = json_encode($data);
            //var_dump($query);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
            $headers[] = 'Content-Length: ' . strlen($query);
        }

        if (!empty($headers)) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $body = curl_exec($curl);

        $result['status_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result['body'] = $body;

        var_dump($result);
        curl_close($curl);
        $data = json_decode($body, 1);

        return $data;
    }

}
/*
$res = WAZZUPAPI::getChannels();
var_dump($res);

$res2 = WAZZUPAPI::sendMessage([
    'channelId' => '6565367f-2456-46ac-a2ed-07eabd1c709e',
    'chatType' => 'whatsapp',
    'chatId' => '79014897145',
    'text' => 'Привет',
]);

var_dump($res2);*/