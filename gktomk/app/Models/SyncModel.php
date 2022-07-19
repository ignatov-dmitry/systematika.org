<?php
## Модель для синхронизации продуктов ГК и абонементов МК

namespace GKTOMK\Models;

class SyncModel
{

    public function __construct()
    {
        DB::init();
    }

    public function createSync($program, $offer, $subscription)
    {
        $sync = DB::dispense('sync');
        $sync->program = $program;
        $sync->gk_offer = $offer; // Оффер в ГК
        $sync->mk_sub = $subscription; // Абонемент в МК
        return DB::store($sync);
    }

    public function editSync($dataSync = [])
    {
        $sync = DB::load('sync', $dataSync['id']);
        if (!empty($dataSync['program']))
            $sync->program = $dataSync['program'];
        if (!empty($dataSync['gk_offer']))
            $sync->gk_offer = $dataSync['gk_offer'];
        if (!empty($dataSync['mk_sub']))
            $sync->mk_sub = $dataSync['mk_sub'];
        return DB::store($sync);
    }

    public function delSync($id){
        //$sync = DB::load('sync', $dataSync['id']);
        DB::trashBatch('sync', [$id]);
    }

    public function getAllSync()
    {
        return DB::exportAll(DB::findAll('sync', 'ORDER BY `id` DESC'));
    }

    public function getSync($filter = [])
    {
        $sql = 'id > 0';
        $data = [];
        if(!empty($filter['gk_offer'])){
            $sql .= ' && gk_offer = :gk_offer';
            $data['gk_offer'] = $filter['gk_offer'];
        }
        if(!empty($filter['mk_sub'])){
            $sql .= ' && mk_sub = :mk_sub';
            $data['mk_sub'] = $filter['mk_sub'];
        }

        return DB::exportAll(DB::find('sync', $sql, $data));
    }

}