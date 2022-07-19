<?php
// Модель для работы
namespace GKTOMK\Models;

use GKTOMK\Models\GetCourse\User;

class GetcourseModel
{

    public function __construct()
    {

    }

    /*
     * Создает пользователя либо обновляет его (если он уже существует)
     * */
    public function createUser($data = [])
    {

        $User = new User();
        // Замените на ваш аккаунт
        $User::setAccountName(CONFIG['gk_account_name']);
        // Замените токен на сгенерированный вашим аккаунтом (http://{your_account}.getcourse.ru/saas/account/api)
        $User::setAccessToken(CONFIG['gk_secret_key']);


        $User = $User
            ->setEmail($data['email'])
            //->setFirstName($data['first_name'])
            //->setLastName($data['last_name'])
            //->setUserAddField('Почтовый адрес', 'New Васюки')
            ->setGroup(trim(CONFIG['gk_prefix_group'] . ' Пришел из МК'))
            ->setOverwrite();
        //->setSessionReferer('http://getcourse.ru')
        if($data['group'] and !empty($data['group'])){
            $User->setGroup(trim(CONFIG['gk_prefix_group'] . ' ' . $data['group']));
        }

        try {
            $result = $User->apiCall($action = 'add');
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }


}