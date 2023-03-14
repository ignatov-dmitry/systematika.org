<?php


namespace GKTOMK\Models\Systematika;


use GKTOMK\Config;
use GKTOMK\Models\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MoyKlass extends SystematikaClient
{
    private static string $urlApi = 'https://api.moyklass.com/';
    private static string $versionApi = 'v1';
    private static string $accessKeyApi = '';
    private static string $accessTokenApi = '';
    private static array $URLs = [];
    private static array $headers = [];

    public function __construct()
    {
        self::$headers = [
            'Content-Type'  => 'application/json'
        ];

        if (empty(CONFIG)) Config::init();
        if (empty(self::$accessKeyApi)) self::$accessKeyApi = CONFIG['mk_api_key'];
        if (empty(self::$accessTokenApi)) $this->getToken(); // Получаем вначале токен

        self::$headers['x-access-token'] =  self::$accessTokenApi;
    }

    private function getToken(): void
    {
        $result = self::callAPI('company/auth/getToken', ['apiKey' => self::$accessKeyApi]);
        self::$accessTokenApi = $result['accessToken'];
    }

    public static function callAPI($url = '', $data = [], $method = 'POST')
    {
        if (!empty($data) and $method == 'GET') {
            $query = urldecode(http_build_query($data));
            $url .= '?' . $query;
            $url = str_replace('[0]', '', $url);
            $url = str_replace('[1]', '', $url);
        }


        $client = new Client(['body' => json_encode($data), 'headers' => self::$headers]);
        $response = $client->request($method, self::$urlApi . self::$versionApi . '/' . $url)->getBody()->getContents();

        return json_decode($response, 1);
    }

    public function addAsyncRoute($url = '', $data = [], $method = 'POST')
    {
        $group = $url;

        if (!empty($data)) {
            $query = urldecode(http_build_query($data));
            $url .= '?' . $query;
            $url = str_replace('[0]', '', $url);
            $url = str_replace('[1]', '', $url);
        }
        //var_dump($url);echo '<br>';
        self::$URLs[$group][] = array(
            'url'    => self::$urlApi . self::$versionApi . '/' . $url,
            'method' => $method
        );
    }

    public function runAsyncRoute($url)
    {
        $client = new Client();
        $data = array();
        $rejectedUrl = array();

        do{
            if (!empty($rejectedUrl)){
                self::$URLs[$url] = $rejectedUrl;
                $rejectedUrl = [];
            }

            $requests = function () use ($url) {
                foreach (self::$URLs[$url] as $URL){
                    yield new Request($URL['method'], $URL['url'], self::$headers);
                }
                self::$URLs[$url] = [];
            };

            $pool = new Pool($client, $requests(), [
                'concurrency' => 50,
                'fulfilled' => function (Response $response, $index) use(&$data) {
                    $data[] = json_decode($response->getBody()->getContents(), 1);
                },
                'rejected' => function (RequestException $reason, $index) use (&$rejectedUrl, $url) {
                    $uriObject = $reason->getRequest()->getUri();
                    $path = $uriObject->getScheme() . '://' . $uriObject->getHost() . $uriObject->getPath() . '?' . $uriObject->getQuery();
                    $rejectedUrl[] = [
                        'url'    => $path,
                        'method' => $reason->getRequest()->getMethod()
                    ];
                }
            ]);

            $promise = $pool->promise();
            $promise->wait();

        } while(!empty($rejectedUrl));


        return $data;
    }

    public function getUsers($filter = [])
    {
        return self::callAPI('company/users/', $filter, 'GET');
    }

    public function getLessons($data = [])
    {
        return self::callAPI('company/lessons', $data, 'GET');
    }

    public function getLessonRecords($data = [])
    {
        return self::callAPI('company/lessonRecords', $data, 'GET');
    }

    public function getClasses($data = [])
    {
        return self::callAPI('company/classes', $data, 'GET');
    }

    public function getSubscriptions($data = [])
    {
        return self::callAPI('company/subscriptions', $data, 'GET');
    }

    public function getUserSubscriptions($data = [])
    {
        return self::callAPI('company/userSubscriptions', $data, 'GET');
    }

    public function getCourses($data = [])
    {
        return self::callAPI('company/courses', $data, 'GET');
    }

    public function insertApiDataToDB($function, $tableName, $async = false, $jsonField = '', $url = '', $clean = false)
    {
        $start = microtime(true);

        $items = array();
        $keys = array();
        $limit = 500;

        if ($clean){
            DB::exec('truncate table ' . $tableName . ';');
        }
        if ($async)
        {
            $filterData = array(
                'limit' => $limit,
                'offset' => 0,
                'sort' => 'id',
                'sortDirection' => 'asc'
            );
            $lastRecord = DB::getRow('SELECT * FROM ' . $tableName . ' ORDER BY id DESC LIMIT 1');
            if ($lastRecord && !in_array($function, ['getLessonRecords', 'getLessons', 'getUserSubscriptions']) && !$clean){
                $lastCreateDate = date('Y-m-d', strtotime($lastRecord['createdAt']));
                $filterData['createdAt[0]'] = $lastCreateDate;
                $filterData['createdAt[1]'] = date('Y-m-d');

                $TotalItems = ($this->$function(['limit' => 1, 'createdAt[0]' => $lastCreateDate, 'createdAt[1]' => date('Y-m-d')]))['stats']['totalItems'];
            }
            elseif ($lastRecord && in_array($function, ['getLessonRecords', 'getLessons']) && !$clean) {
                $filterData['date[0]'] = date('Y-m-d', strtotime(date('Y-m-d') . '-4 days'));
                $filterData['date[1]'] = date('Y-m-d');
                $TotalItems = ($this->$function(['limit' => 1, 'date[0]' => $filterData['date[0]'], 'date[1]' => $filterData['date[1]']]))['stats']['totalItems'];
            }
            elseif ($lastRecord && $function == 'getUserSubscriptions' && !$clean) {
                $filterData['sellDate[0]'] = date('Y-m-d', strtotime(date('Y-m-d') . '-4 days'));
                $filterData['sellDate[1]'] = date('Y-m-d');
                $TotalItems = ($this->$function(['limit' => 1, 'sellDate[0]' => $filterData['sellDate[0]'], 'sellDate[1]' => $filterData['sellDate[1]']]))['stats']['totalItems'];
            }
            else {
                $TotalItems = ($this->$function(['limit' => 1]))['stats']['totalItems'];
            }


            for ($offset = 0; $offset < $TotalItems; $offset += $limit) {
                if ($TotalItems < $offset)
                    $offset = $TotalItems;

                $filterData['offset'] = $offset;
                $this->addAsyncRoute($url, $filterData, 'GET');
            }

            $data = $this->runAsyncRoute($url);

            foreach ($data as $response)
            {
                foreach (DB::getColumnValues($response[$jsonField], DB::getInstance()->getTableColumn($tableName)) as $item) {
                    $keys = array_keys($item);
                    $items[] = $item;
                }
            }
        }

        else {
            $data = $this->$function();
            foreach (DB::getColumnValues($data, DB::getInstance()->getTableColumn($tableName)) as $item) {
                $keys = array_keys($item);
                $items[] = $item;
            }
        }


        $sql = DB::getInstance()->prepareBulkInsert($tableName, $keys, $items, true);
        DB::exec($sql);
        echo 'Время выполнения скрипта для : "' . $url . '" ' . round(microtime(true) - $start, 4) . ' сек.<br>';
    }
}
