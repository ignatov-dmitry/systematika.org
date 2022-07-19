<?php
// Модель для работы
namespace GKTOMK\Models;

use GKTOMK\Models\GetCourse\User;

class GetcourseModel
{

    private $ObjectUser = ''; // Здесь хранится объект модели GK
    private $DataUser = ''; // Здесь хранится объект с заполненными данными юзера

    public function __construct()
    {
        $this->ObjectUser = new User();
        // Замените на ваш аккаунт
        $this->ObjectUser::setAccountName(CONFIG['gk_account_name']);
        // Замените токен на сгенерированный вашим аккаунтом (http://{your_account}.getcourse.ru/saas/account/api)
        $this->ObjectUser::setAccessToken(CONFIG['gk_secret_key']);
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
            ->setGroup(trim(CONFIG['gk_prefix_group'] . ' Пришел из МК'))
            ->setOverwrite();
        //->setSessionReferer('http://getcourse.ru')
        if(isset($data['group']) and !empty($data['group'])){
            $User->setGroup(trim(CONFIG['gk_prefix_group'] . ' ' . $data['group']));
        }

        // Ставим дату последнего посещения занятия
        if(isset($data['date_last_lesson']) and !empty($data['date_last_lesson']) and !empty(CONFIG['gk_field_date_last_lesson'])){
            $User->setUserAddField(CONFIG['gk_field_date_last_lesson'], $data['date_last_lesson']);
        }

        // Ставим дату последнего пробного урока в доп поле ГК
        if(isset($data['date_last_test_lesson']) and !empty($data['date_last_test_lesson']) and !empty(CONFIG['gk_field_date_last_test_lesson'])){
            $User->setUserAddField(CONFIG['gk_field_date_last_test_lesson'], $data['date_last_test_lesson']);
        }

        // Заполняем поле количество абонементов
        if(isset($data['count_user_subscriptions'])){
            $User->setUserAddField(CONFIG['gk_field_count_user_subscriptions'], $data['count_user_subscriptions']);
        }

        // Заполняем поле количество оставшихся посещений
        if(isset($data['user_subscriptions_left_visits'])){
            $User->setUserAddField(CONFIG['gk_field_user_subscriptions_left_visits'], $data['user_subscriptions_left_visits']);
        }

        try {
            $result = $User->apiCall($action = 'add');
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    public function init($data = []){



        $this->DataUser = $this->ObjectUser
            ->setEmail($data['email'])
            //->setFirstName($data['first_name'])
            //->setLastName($data['last_name'])
            ->setGroup(trim(CONFIG['gk_prefix_group'] . ' Пришел из МК'))
            ->setOverwrite();

        if(isset($data['groups']) and count($data['groups']) > 0){
            foreach ($data['groups'] as $group) {
                $this->DataUser->setGroup(trim(CONFIG['gk_prefix_group'] . ' ' . $group));
            }
        }

        if(isset($data['fields']) and count($data['fields']) > 0){
            foreach ($data['fields'] as $name => $value) {
                $this->DataUser->setUserAddField($name, $value);
            }
        }

        return $this;
    }

    public function send(){
        try {
            $result = $this->DataUser->apiCall($action = 'add');
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /*
     * Обновляет дату посещений в полях ГК
     *
     * */
    public function updateUserDateVisit($email){

        $userMk = MoyklassModel::getUserByEmail($email);

        if(!isset($userMk) or !isset($userMk['email']))
            return 'mk user not found';

        $dataUpdate = [
            'email' => $userMk['email'],
        ];

        // Обновляем дату последнего пробного
        $lesson_last_test = MoyklassModel::getLessonVisitLastTest($userMk['id']);
        if (isset($lesson_last_test) and !empty($lesson_last_test)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last_test['date']));
            $dataUpdate['date_last_test_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $dataUpdate['date_last_test_lesson'] = '01.01.1970';
        }

        // Дата последнего посещения урока
        $lesson_last = MoyklassModel::getLessonVisitLast($userMk['id']);
        if (isset($lesson_last) and !empty($lesson_last)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date']));
            $dataUpdate['date_last_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $dataUpdate['date_last_lesson'] = '01.01.1970';
        }

        return $this->createUser($dataUpdate);
    }

    public function updateUserSubscriptions($email){
        $userMk = MoyklassModel::getUserByEmail($email);

        if(!isset($userMk) or !isset($userMk['email']))
            return 'mk user not found';

        $dataUpdate = [
            'email' => $userMk['email'],
        ];

        // Обновляем количество абонементов у клиента
        $user_subscriptions = MoyklassModel::getUserSubscriptions(['userId' => $userMk['id'], 'statusId' => '2']);
        if (!empty($user_subscriptions['subscriptions'])) {
            $dataUpdate['count_user_subscriptions'] = $user_subscriptions['stats']['totalItems'];
            $dataUpdate['user_subscriptions_left_visits'] = ($user_subscriptions['stats']['totalVisits'] - $user_subscriptions['stats']['totalVisited']);
        }

        return $this->createUser($dataUpdate);
    }




}