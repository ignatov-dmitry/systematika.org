<?php


namespace App\Models\Offers;


class TariffsModel extends OffersModel
{
    public function createTariff(){
        R::dispense('tariffs');

    }

}