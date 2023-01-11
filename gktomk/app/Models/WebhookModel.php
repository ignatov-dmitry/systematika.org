<?php


namespace GKTOMK\Models;


class WebhookModel
{
    public function __construct()
    {
        DB::init();
    }

    public function editLogWebhook($data = []){

        if (empty($data['id'])) {
            $logwebhook = DB::dispense('logwebhook');
        } else {
            $logwebhook = DB::load('logwebhook', $data['id']);
        }

        foreach ($data as $key => $value) {
            if($key == 'id')
                continue;
            $logwebhook->{$key} = $value;
        }

        return DB::store($logwebhook);

    }

}