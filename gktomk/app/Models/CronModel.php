<?php


namespace GKTOMK\Models;


use GKTOMK\Models\Events\CronEvents;

class CronModel
{
    public function __construct()
    {
        DB::init();
    }

    public function getCronEventsActual(){
        $time = time();
        return DB::getAll('SELECT `name` FROM `cronevent` WHERE `last_launch`+`period`<:time && `period`>0 ORDER BY `last_launch` ASC', ['time' => $time]);
    }

    public function getCronByTask($task){
        return DB::getRowByKey('cronevent', 'name', $task, ['*']);
    }

    public function setAddCronEvent($data = []){
        $cronevent = DB::dispense('cronevent');
        $cronevent->name = $data['name'];
        $cronevent->last_launch = 0;
        $cronevent->period = $data['period'];
        DB::store($cronevent);
    }

    public function setUpdateLastLaunchByCronId($id){
        /*$cronevent = DB::load('cronevent', $id);
        $cronevent->last_launch = time();
        DB::store($cronevent);*/
        $this->setUpdateByCronId($id, ['last_launch' => time()]);
    }

    public function setUpdateByCronId($id, $data = []){
        $cronevent = DB::load('cronevent', $id);
        foreach ($data as $key => $value) {
            $cronevent->{$key} = $value;
        }
        DB::store($cronevent);
    }

    private function runCronByTask($task)
    {
        $CronEvents = new CronEvents([]);
        $CronEvents->request['event'] = $task;
        return $CronEvents->handle();
    }

    public function startCronByTask($task)
    {
        $taskData = $this->getCronByTask($task);
        var_dump($taskData);
        if(empty($taskData))
            return false;

        if($taskData['run']==1 && (time() < ($taskData['last_launch']+($taskData['period']+5)+$taskData['time_run_max'])))
            return false;

        $this->setUpdateByCronId($taskData['id'], ['run' => 1, 'last_launch' => time()]);
        $time_run_start = microtime(true);
        $this->runCronByTask($task);
        $time_run = round(microtime(true) - $time_run_start, 5);


        $dataUpdate = ['run' => 0, 'time_run' => $time_run];

        if($taskData['time_run_max'] < $time_run){
            $dataUpdate['time_run_max'] = $time_run;
        }


        if($taskData['time_run_min'] > $time_run or empty($taskData['time_run_min'])) {
            $dataUpdate['time_run_min'] = $time_run;
        }

        $this->setUpdateByCronId($taskData['id'], $dataUpdate);


    }

    /*
     * Запускает задания крона
     * */
    public function startCron(){


        $last_launch = DB::getOption('systemsetting', 'cron_last_launch');
        $time_run_max = DB::getOption('systemsetting', 'cron_time_run_max');

        if(
            DB::getOption('systemsetting', 'cron_run') == 1
            and
            (time() < ($last_launch+$time_run_max))
        )
            return false;

        DB::setOption('systemsetting', 'cron_run', 1);
        DB::setOption('systemsetting', 'cron_last_launch', time());

        $actuals = $this->getCronEventsActual();

        $CronEvents = new CronEvents([]);

        foreach ($actuals as $actual) {
            /*$CronEvents->request['event'] = $actual['name'];
            $CronEvents->handle(); // Запускаем обработчик крона
            $this->setUpdateLastLaunchByCronId($actual['id']); // Отмечаем, что запуск состоялся
        */
            $this->startCronByTask($actual['name']);
        }

        DB::setOption('systemsetting', 'cron_run', 0);

        $time_run = round(microtime(true) - GLOBAL_TIMER, 5);
        DB::setOption('systemsetting', 'cron_time_run', $time_run);
        if($time_run_max < $time_run)
            DB::setOption('systemsetting', 'cron_time_run_max', $time_run);

        $time_run_min = DB::getOption('systemsetting', 'cron_time_run_min');
        if($time_run_min > $time_run or empty($time_run_min))
            DB::setOption('systemsetting', 'cron_time_run_min', $time_run);

    }
}