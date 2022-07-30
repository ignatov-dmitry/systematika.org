<?php


namespace GKTOMK\Models;

use Firebase\JWT\JWT;

class ZoomModel
{
    private $api_key;
    private $api_secret;
    private $jwt = ['code', 'timeleft'];

    public function __construct($api_key = '', $api_secret = '')
    {
        $this->api_key = CONFIG['zoom_api']['key'];
        $this->api_secret = CONFIG['zoom_api']['secret'];
    }

    public function getRecords(){

    }

    private function genJWT()
    {

        if($this->jwt['timeleft'] > time() and !empty($this->jwt['code']))
            return $this->jwt['code'];

        $timeleft = time()+1;

        $payload = array(
            "iss" => $this->api_key,
            "exp" => $timeleft * 1000
        );

        $this->jwt['code'] = JWT::encode($payload, $this->api_secret);
        $this->jwt['timeleft'] = $timeleft;

        return $this->jwt['code'];
    }


    public function getUsers($data = []){
        // status=active&page_size=30&page_number=1
        return $this->call("users/", $data);

    }

    public function getRecordings($userId, $data = [])
    {
        return $this->call("users/{$userId}/recordings", $data);
    }

    public function getLinkDownloadByUrl($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url .'?access_token='.$this->genJWT(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        preg_match_all('#(?:https?|ftp)://ssr[^\s\,]+#i', $header, $matches);
        return $matches[0][0];
    }

    public function getRecordingByMeetingId($meetingId, $data = [])
    {
        return $this->call("meetings/{$meetingId}/recordings", $data);
    }



    private function call($url, $data = []){
        $curl = curl_init();

       $url = "https://api.zoom.us/v2/". $url . "?" .http_build_query($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_HEADER => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer ".$this->genJWT(),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close($curl);

        if ($err) {
            return ["error" => "cURL Error #:" . $err];
        } else {
            $body = json_decode($body, 1);
        }
        //return ['header' => $header, 'body' => $body];
        return $body;
    }

}