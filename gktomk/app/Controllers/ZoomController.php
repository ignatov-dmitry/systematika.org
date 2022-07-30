<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\ZoomaccountsModel;
use GKTOMK\Models\ZoomModel;

class ZoomController
{

    public function main(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.zoom.us/v2/users/me/recordings?status=active&page_size=100&page_number=1",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJwZlRyMUlsRFM2cW5wSFdBcTVUUjdBIiwiZXhwIjoxNjM0NjQyNjExMDAwfQ.SLPjsujixCSoNWQ7YrAK8ZBTHgOL-xvYkmnexpTsPuU",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $json = json_decode($response, 1);
            print_r($json);
        }
    }

    public function getDownload(){
        // https://us02web.zoom.us/rec/download/QvL_APhwdFIpu81J7LIQunrg5MRc7zBZzzmkiRO3I-Bxfk_dRcqFiw-BtnI7AsOC7q1zVSnZ9BEnjjih.wnLURjiwGl3vz_l9


        //$ch = curl_init('https://us02web.zoom.us/rec/download/WsE83cBcaB0bE_le_eSCudE_hkDRXtDirAPWizYvPxZyBFHk7xHo8UjDFuLHIHrgJcQZZJQtFUx5Ij6_.rHD87RfLgqqvWoBv');
        $ch = curl_init('https://us02web.zoom.us/rec/download/SrF4t0L9Gy71WP_UpnAt1cbxdPSTCF81Ar1VWcAn7BTUI6p83V4GwsxSLoXozYxqrKbxosZkwxoO8nfi.fABQgkCoXJ_iDGBv');
        //$fp = fopen(__DIR__ . '/../../zoom/record2.mp4', 'wb');
        //curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $resp = curl_exec($ch);

        $err = curl_error($ch);
        curl_close($ch);
        //fclose($fp);

        var_dump($resp);


    }

    public function getHeader()
    {
        $resp = get_headers('https://us02web.zoom.us/rec/download/WsE83cBcaB0bE_le_eSCudE_hkDRXtDirAPWizYvPxZyBFHk7xHo8UjDFuLHIHrgJcQZZJQtFUx5Ij6_.rHD87RfLgqqvWoBv');
        var_dump($resp);
    }

    public function getTest(){

        $Zoomaccounts = new ZoomaccountsModel();
        $accountData = $Zoomaccounts->getAccountDataById(2);
        $ZoomModel = new ZoomModel($accountData['api_key'], $accountData['api_secret']);
        $res = $ZoomModel->getUsers([
            'status' => 'active',
            'page_size' => '30',
            'page_number' => 1
        ]);

        var_dump($res);

        $res = $ZoomModel->getRecordings('-ue9gvnbSjGoGyu11cF9Jw', [
            'from' => '2021-10-18',
            'to' => '2021-10-19',
        ]);

        var_dump($res);

        $recordings = $ZoomModel->getRecordingByMeetingId('11lNHf1hSTiYICBeqoJGog==')['body'];

        var_dump($recordings);

        foreach ($recordings['recording_files'] as $recording_file) {
            if($recording_file['recording_type'] == 'shared_screen_with_speaker_view')
                $download_url = $recording_file['download_url'];
        }

        // Получили прямую ссылку на скачивание
        $ZoomModel->getLinkDownloadRecordByUrl($download_url);


        var_dump($res);
    }

    public function getVideo(){
        $video = $_GET['v'];

        $file = $video;
        $fp = @fopen($file, 'rb');
        $size = filesize($file); // File size
        $length = $size; // Content length
        $start = 0; // Start byte
        $end = $size - 1; // End byte
        header('Content-type: video/mp4');
        //header("Accept-Ranges: 0-$length");
        header("Accept-Ranges: bytes");
        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end = $end;
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }

            if ($range == '-') {
                $c_start = $size - substr($range, 1);
            }else{
                $range = explode('-', $range);
                $c_start = $range[0];
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
            }
            $c_end = ($c_end > $end) ? $end : $c_end;

            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                exit;
            }
            $start = $c_start;
            $end = $c_end;
            $length = $end - $start + 1;
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
        }
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: ".$length);
        $buffer = 1024 * 8;
        while(!feof($fp) && ($p = ftell($fp)) <= $end) {
            if ($p + $buffer > $end) {
                $buffer = $end - $p + 1;
            }
            set_time_limit(0);
            echo fread($fp, $buffer);
            flush();
        }
        fclose($fp);
        exit();

    }

}