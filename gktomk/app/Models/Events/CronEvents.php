<?php


namespace GKTOMK\Models\Events;


use GKTOMK\Models\Events;
use GKTOMK\Models\StatisticsModel;

class CronEvents extends Events
{


    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        if (method_exists($this, $this->request['event'])) {
            return $this->{$this->request['event']}();
        }
    }

    private function visitslesson_everyday(){
        $stats = new StatisticsModel();

        $time = time();
        $period = $time - (60*60*24*7); // Обновляем на 7 дней назад
        $datestart = date("Y-m-d", $period);
        $dateend = date("Y-m-d", $time);

        $stats->getLoadVisits($datestart, $dateend);
    }

    private function visitslesson_everyhour(){
        $stats = new StatisticsModel();

        $time = time();
        $period = $time + (60*60*24*7); // Обновляем на 7 дней вперед
        $datestart = date("Y-m-d", $time);
        $dateend = date("Y-m-d", $period);

        $stats->getLoadVisits($datestart, $dateend);
    }

    






}