<?php

namespace GKTOMK\Models\GetCourse;

use Exception;
use GKTOMK\Models\GetCourse\core\Core;
use GKTOMK\Models\GetCourse\core\Model;

class Account extends Model
{
    private string $status = 'active';



    public function getExportKey()
    {

    }

    public function getUsers($exportKey)
    {

    }

    /**
     * @throws Exception
     */
    public function apiCall($action, $params = [] ) {
        $curl = curl_init(self::getUrl().'account/' . $action);

        $params['key'] = self::getAccessToken();
        curl_setopt ($curl, CURLOPT_USERAGENT, 'GETCOURSE-PHP-SDK');
        curl_setopt ($curl, CURLOPT_POST, 1);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $body = curl_exec ($curl);

        $result = new \StdClass();
        $result->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result->body = $body;
        curl_close ($curl);

        return json_decode($result->body);
    }
}