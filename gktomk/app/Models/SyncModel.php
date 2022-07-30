<?php
## Модель для синхронизации продуктов ГК и абонементов МК

namespace GKTOMK\Models;

class SyncModel
{

    public function __construct()
    {
        DB::init();
    }

    public function createSync($program, $offer, $subscription, $demo, $individual)
    {
        $sync = DB::dispense('sync');
        $sync->program = $program;
        $sync->gk_offer = $offer; // Оффер в ГК
        $sync->mk_sub = $subscription; // Абонемент в МК
        $sync->demo = $demo;
        $sync->individual = $individual; // Обозначает как индивидуальный абонемент
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

        $sync->demo = $dataSync['demo'];
        $sync->individual = $dataSync['individual'];

        return DB::store($sync);
    }

    public function delSync($id)
    {
        //$sync = DB::load('sync', $dataSync['id']);
        DB::trashBatch('sync', [$id]);
    }

    public function getAllSync()
    {
        return DB::exportAll(DB::findAll('sync', 'ORDER BY `id` DESC'));
    }

    public function getSyncOnlyMkSubIds($filter = ['gk_offer', 'mk_sub', 'individual'])
    {
        $syncs = $this->getSync($filter);
        $ids = [];
        foreach ($syncs as $sync) {
            $ids[] = $sync['mk_sub'];
        }
        return $ids;
    }

    public function getSync($filter = ['gk_offer', 'mk_sub', 'individual'])
    {
        $sql = 'id > 0';
        $data = [];
        if (!empty($filter['gk_offer'])) {
            $sql .= ' && gk_offer = :gk_offer';
            $data['gk_offer'] = $filter['gk_offer'];
        }
        if (!empty($filter['mk_sub'])) {
            $sql .= ' && mk_sub = :mk_sub';
            $data['mk_sub'] = $filter['mk_sub'];
        }
        if (!empty($filter['individual'])) {
            $sql .= ' && individual = 1';
        }

        return DB::exportAll(DB::find('sync', $sql, $data));
    }

}