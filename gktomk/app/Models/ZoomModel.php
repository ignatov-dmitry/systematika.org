<?php


namespace GKTOMK\Models;

use Firebase\JWT\JWT;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\Util;

class ZoomModel extends Model
{
    private $api_key;
    private $api_secret;
    private $jwt = ['code', 'timeleft'];

    public function __construct($api_key = null, $api_secret = null)
    {
        $this->api_key = $api_key ?: CONFIG['zoom_api']['key'];
        $this->api_secret = $api_secret ?: CONFIG['zoom_api']['secret'];
    }

    public function getRecords(){

    }

    public function getAccessToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=E1RvNLEPQ_eVsEdWVJMg8w',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode('buweHgtPS56Kg12lHmvJ_A:KzpqptjmJuVEaERFr4TkJE32ZkR1vAYb'),
                ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response)->access_token;
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
        //TODO Преврить работает ли новая реализация вместо $this->genJWT()
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url .'?access_token='.$this->getAccessToken(),
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

    public function deleteMeeting($meetingId, $data = [])
    {
        return $this->call("meetings/{$meetingId}/recordings", $data, 'DELETE');
    }

    private function call($url, $data = [], $method = 'GET'){
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
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $this->getAccessToken(),
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

    public function createZoomMeetings($meetings)
    {
        $zoomRecords = array();
        $zoomMeetings = array();
        $zoomMeetingKeys = Model::getInstance()->getTableColumn('zoom_meetings');
        $zoomMeetingRecordsKeys = Model::getInstance()->getTableColumn('zoom_meeting_records');

        $zoomRecordsForMeetings = array_column($meetings, 'recording_files');
        foreach ($zoomRecordsForMeetings as $zoomRecordsForMeeting)
            foreach ($zoomRecordsForMeeting as $zoomRecord)
            {
                $zoomRecord['recording_start'] = date('Y-m-d H:i:s', strtotime($zoomRecord['recording_start']));
                $zoomRecord['recording_end'] = date('Y-m-d H:i:s', strtotime($zoomRecord['recording_end']));
                $zoomRecords[] = $zoomRecord;
            }

        foreach (Model::getColumnValues($meetings, $zoomMeetingKeys) as $meeting){
            $meeting['start_time'] = date('Y-m-d H:i:s', strtotime($meeting['start_time']));
            $zoomMeetings[] = $meeting;
        }

        $zoomRecords = Model::getColumnValues($zoomRecords, $zoomMeetingRecordsKeys, ['status' => 'zoom_status'], ['download_status' => 'not_started', 'try_num' => 0]);

        $sqlZoomMeetings = $this->prepareBulkInsert('zoom_meetings', $zoomMeetingKeys, $zoomMeetings, true);
        $sqlZoomRecords = $this->prepareBulkInsert('zoom_meeting_records', $zoomMeetingRecordsKeys, $zoomRecords, true);

        DB::exec($sqlZoomMeetings);
        DB::exec($sqlZoomRecords);

        return true;
    }

    public function getZoomMeetings($criteria = array(), $orderBy = null, $limit = null, $offset = null)
    {
        $sql = "
            SELECT DISTINCT zmr.*, zm.topic, zm.start_time FROM {records}  AS zmr
            LEFT JOIN {meetings} AS zm ON zm.uuid = zmr.meeting_id
            {condition} 
        ";

        if ($orderBy) $sql .= ' ORDER BY {order}';
        if ($limit) $sql .= ' LIMIT {limit}';
        if ($offset) $sql .= ' OFFSET {offset}';


        $whereCondition = $this->prepareWhere($criteria);

        $sql = Util::replaceTokens($sql, [
            'records'   => 'zoom_meeting_records',
            'meetings'  => 'zoom_meetings',
            'condition' => $whereCondition ? ' WHERE ' . $whereCondition : '',
            'order'     => $orderBy,
            'limit'     => $limit,
            'offset'    => $offset
        ]);
        return DB::getAll($sql);
    }

    public function getCountZoomMeetings($criteria = array(), $orderBy = null, $limit = null)
    {
        $sql = "
            SELECT COUNT(DISTINCT zmr.id) as count FROM {records}  AS zmr
            LEFT JOIN {meetings} AS zm ON zm.uuid = zmr.meeting_id
            {condition} 
        ";

        if ($orderBy) $sql .= ' ORDER BY {order}';
        if ($limit) $sql .= ' LIMIT {limit}';



        $whereCondition = $this->prepareWhere($criteria);

        $sql = Util::replaceTokens($sql, [
            'records'   => 'zoom_meeting_records',
            'meetings'  => 'zoom_meetings',
            'condition' => $whereCondition ? ' WHERE ' . $whereCondition : '',
            'order'     => $orderBy,
            'limit'     => $limit
        ]);

        $result = DB::getRow($sql);

        return $result['count'];
    }

    public function getCountVideosFromMeeting($meetingId): int
    {
        $sql = "
            SELECT COUNT(*) AS count FROM {tableName}
            WHERE {condition};
        ";

        $condition = [
            'meeting_id'     => $meetingId,
            'zoom_status'    => 'completed',
            'file_extension' => 'MP4',
            array('key' => 'download_status', 'val' => ['downloaded'], 'op' => Model::OP_NOTIN),
        ];

        $whereCondition = $this->prepareWhere($condition);

        $sql = Util::replaceTokens($sql, [
            'tableName' => 'zoom_meeting_records',
            'condition' => $whereCondition
        ]);

        return (int)DB::getCell($sql);
    }

    public function downloadByLink($link, $dirName = '', $fileName = '', $fileExtension= 'MP4')
    {

        $dir = __DIR__ . '/../../';
        $dirToFile = $dir . $dirName;

        if (!file_exists($dirToFile) and is_writable($dir)) {
            if (!mkdir($dirToFile, 0777, true))
                return 'Error create dir';
        } elseif (!file_exists($dirToFile) and !is_writable($dir)) {
            return 'Dir not found or not writable.';
        }
        $fileName = $fileName . '.' . $fileExtension;

        $cmd = "cd {$dirToFile}; curl -o '{$fileName}' -k '{$link}'; echo 'download'; > /dev/null";

        $res = shell_exec($cmd);

        $dirFile = $dirToFile . '/' . $fileName;
        if (file_exists($dirFile))
            return 'downloaded';
        else
            return 'error';

    }

    public function setStatusRecordById($recordId, $status)
    {
        $this->setDataRecord($recordId, 'download_status', $status);
        return $status;
    }

    public function setDataRecord($recordId, $key, $value)
    {
        $record = DB::load('zoom_meeting_records', $recordId);
        $record->{$key} = $value;
        DB::store($record);
    }
}