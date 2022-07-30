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
        return DB::getAll('SELECT * FROM `cronevent` WHERE `last_launch`+`period`<:time && `period`>0 ORDER BY `last_launch` ASC', ['time' => $time]);
    }

    public function setAddCronEvent($data = []){
        $cronevent = DB::dispense('cronevent');
        $cronevent->name = $data['name'];
        $cronevent->last_launch = 0;
        $cronevent->period = $data['period'];
        DB::store($cronevent);
    }

    public function setUpdateLastLaunchByCronId($id){
        $cronevent = DB::load('cronevent', $id);
        $cronevent->last_launch = time();
        DB::store($cronevent);
    }

    public function setCronByTask($task)
    {
        $CronEvents = new CronEvents([]);
        $CronEvents->request['event'] = $task;
        return $CronEvents->handle();
    }
    /*
     * Запускает задания крона
     * */
    public function startCron(){
        $actuals = $this->getCronEventsActual();

        $CronEvents = new CronEvents([]);

        foreach ($actuals as $actual) {
            $CronEvents->request['event'] = $actual['name'];
            $CronEvents->handle(); // Запускаем обработчик крона
            $this->setUpdateLastLaunchByCronId($actual['id']); // Отмечаем, что запуск состоялся
        }
    }
}