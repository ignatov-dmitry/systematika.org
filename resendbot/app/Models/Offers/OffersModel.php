<?php
/*Модель управления предложениями*/

namespace App\Models\Offers;

use App\Lib\DBN;
use RedBeanPHP\R as R;

class OffersModel
{

    public function __construct()
    {
        DBN::init(); // соединение с бд
    }

    /*
     * Создает новое предложение
    */
    public function createOffer(array $data)
    {
        $sql = R::dispense('offers');
        !empty($data['name']) ? $sql->name = $data['name'] : '';
        !empty($data['title']) ? $sql->title = $data['title'] : '';
        !empty($data['description']) ? $sql->description = $data['description'] : '';
        return R::store($sql);
    }

    /*
     * Отдает информацию об оффере
     *
     * */

    public function getOfferById($id)
    {
        return R::load('offers', $id);
    }

}