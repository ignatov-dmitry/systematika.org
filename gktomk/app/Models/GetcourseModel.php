<?php
// Модель для работы
namespace GKTOMK\Models;

use Exception;
use GKTOMK\Models\GetCourse\User;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\MoyKlass\Lesson;
use GKTOMK\Models\Systematika\MoyKlass\UserSubscription;

class GetcourseModel
{

    private $ObjectUser = ''; // Здесь хранится объект модели GK
    private $DataUser = []; // Здесь хранится объект с заполненными данными юзера

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
            ->setGroup(trim('Пришел из МК')) // CONFIG['gk_prefix_group'] .
            ->setOverwrite();
        //->setSessionReferer('http://getcourse.ru')
        if(isset($data['group']) and !empty($data['group'])){
            $User->setGroup(trim($data['group'])); // CONFIG['gk_prefix_group'] . ' ' .
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

        // Заполняем поле количество оставшихся посещений индивидуальных
        if(isset($data['user_subscriptions_left_visits_individual'])){
            $User->setUserAddField(CONFIG['gk_field_user_subscriptions_left_visits_individual'], $data['user_subscriptions_left_visits_individual']);
        }

        // Заполняем поле количество оставшихся посещений групповых
        if(isset($data['user_subscriptions_left_visits_group'])){
            $User->setUserAddField(CONFIG['gk_field_user_subscriptions_left_visits_group'], $data['user_subscriptions_left_visits_group']);
        }

        // Следующее бесплатное занятие
        $User->setUserAddField(CONFIG['gk_field_next_free_recording'], $data['date_next_free_lesson']);

        // Следующее платное занятие
        $User->setUserAddField(CONFIG['gk_field_next_paid_recording'], $data['date_next_paid_lesson']);

        // Дата последнего пропуска занятия
        if(isset($data['date_last_skip_lesson'])){
            $User->setUserAddField(CONFIG['gk_field_date_skip_lesson'], $data['date_last_skip_lesson']);
        }

        try {
            $result = $User->apiCall($action = 'add');
        } catch (Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    public static function updateUser($object = null)
    {
        $stats = $object['stats'];
        $User = new User();
        $User::setAccountName(CONFIG['gk_account_name']);
        $User::setAccessToken(CONFIG['gk_secret_key']);

        $member = new MemberModel();
        $memberEmail = $member->getMemberByMkUid($object['userId'])['email'];

        $User = $User
            ->setEmail($memberEmail)
            ->setOverwrite();

        $lessonLast = DB::getRow('SELECT l.date
            FROM member as m
            left join recordslesson as rl on rl.user_id_mk = m.mk_uid
            left join lessons as l on l.lesson_id_mk = rl.lesson_id_mk
            WHERE rl.visit = 1 and m.email = \'' . $memberEmail . '\' and l.date < NOW() order by l.date desc limit 1')['date'];

        $lessonNextFree = DB::getRow('SELECT l.date
            FROM member as m
            left join recordslesson as rl on rl.user_id_mk = m.mk_uid
            left join lessons as l on l.lesson_id_mk = rl.lesson_id_mk
            WHERE rl.free = 1 and m.email = \'' . $memberEmail . '\' AND l.date > NOW() order by l.date limit 1')['date'];

        $lessonSkipLast = DB::getRow('SELECT l.date
            FROM member as m
            left join recordslesson as rl on rl.user_id_mk = m.mk_uid
            left join lessons as l on l.lesson_id_mk = rl.lesson_id_mk
            WHERE rl.visit = 0 and m.email = \'' . $memberEmail . '\' and l.date < NOW() order by l.date desc limit 1')['date'];

        $lessonSkipLastTest = DB::getRow('SELECT l.date
            FROM member as m
            left join recordslesson as rl on rl.user_id_mk = m.mk_uid
            left join lessons as l on l.lesson_id_mk = rl.lesson_id_mk
            WHERE rl.visit = 0 and rl.test = 1 and m.email = \'' . $memberEmail . '\' and l.date < NOW() order by l.date desc limit 1')['date'];

        $lessonLastTest = DB::getRow('SELECT l.date
            FROM member as m
            left join recordslesson as rl on rl.user_id_mk = m.mk_uid
            left join lessons as l on l.lesson_id_mk = rl.lesson_id_mk
            WHERE rl.visit = 1 and rl.test = 1 and m.email = \'' . $memberEmail . '\' and l.date < NOW() order by l.date desc limit 1')['date'];


        // Последнее посещение занятие
        if($lessonLast)
            $User->setUserAddField(CONFIG['gk_field_date_last_lesson'], (new \DateTime($lessonLast))->format('d.m.Y') ?? '');

        // Последнее пробное занятие
        if($lessonLastTest)
            $User->setUserAddField(CONFIG['gk_field_date_last_test_lesson'], (new \DateTime($lessonLastTest))->format('d.m.Y') ?? '');

        // Последнее пропущенное занятие
        if ($lessonSkipLast)
            $User->setUserAddField(CONFIG['gk_field_date_skip_lesson'],(new \DateTime($lessonSkipLast))->format('d.m.Y') ?? '');

        // Последнее пропущенное пробное занятие
        if ($lessonSkipLastTest)
            $User->setUserAddField(CONFIG['gk_field_date_missing_free_test'],(new \DateTime($lessonSkipLastTest))->format('d.m.Y') ?? '');

        // Следующее платное занятие
        if(isset($stats['nextRecord']) and !empty($stats['nextRecord']) and !empty(CONFIG['gk_field_next_paid_recording']))
            $User->setUserAddField(CONFIG['gk_field_next_paid_recording'],(new \DateTime($stats['nextRecord']))->format('d.m.Y') ?? '');

        // Следующее бесплатное занятие
        if ($lessonNextFree)
            $User->setUserAddField(CONFIG['gk_field_next_free_recording'],(new \DateTime($lessonNextFree))->format('d.m.Y') ?? '');


        try {
            $result = $User->apiCall($action = 'add');
        } catch (Exception $e) {
            $result = $e->getMessage();
        }
    }
    /*
     * Нужен для ручной отправки в гк с заполненными данными
     * */
    public function sendUser(){
        return $this->createUser($this->DataUser);
    }

    public function init($data = []){



        $this->DataUser = $this->ObjectUser
            ->setEmail($data['email'])
            //->setFirstName($data['first_name'])
            //->setLastName($data['last_name'])
            ->setGroup(trim('Пришел из МК')) // CONFIG['gk_prefix_group'] .
            ->setOverwrite();

        if(isset($data['groups']) and count($data['groups']) > 0){
            foreach ($data['groups'] as $group) {
                $this->DataUser->setGroup(trim($group)); // CONFIG['gk_prefix_group'] . ' ' .
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

    public function setEmail($email){
        $this->DataUser['email'] = $email;
        return $this;
    }

    /*
     * Обновляет дату посещений в полях ГК
     *
     * */
    public function updateUserDateVisitByUserIdMK($userId){
        // Обновляем дату последнего пробного
        $lesson_last_test = MoyklassModel::getLessonVisitLastTest($userId);

        if (isset($lesson_last_test) and !empty($lesson_last_test)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last_test['date']));
            $this->DataUser['date_last_test_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $this->DataUser['date_last_test_lesson'] = '01.01.1970';
        }

        // Дата последнего посещения урока
        $lesson_last = MoyklassModel::getLessonVisitLast($userId);
        if (isset($lesson_last) and !empty($lesson_last)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date']));
            $this->DataUser['date_last_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $this->DataUser['date_last_lesson'] = '01.01.1970';
        }

        $lessonNextPaid = MoyklassModel::getNextPaidAndFreeRecording($userId);
        if (isset($lessonNextPaid) and !empty($lessonNextPaid)) {
            $this->DataUser['date_next_paid_lesson'] = $lessonNextPaid['date_next_paid_lesson'];
            $this->DataUser['date_next_free_lesson'] = $lessonNextPaid['date_next_free_lesson'];
        }
        return $this;
    }


    public function updateUserDateVisit($email){

        $userMk = MoyklassModel::getUserByEmail($email);
       // $userMk = (new \GKTOMK\Models\Systematika\MoyKlass\User())->getItem(['email' => $email]);


        if(!isset($userMk) or !isset($userMk['email']))
            return 'mk user not found';

        $this->DataUser['email'] = $userMk['email'];

        // Обновляем дату последнего пробного
        $lesson_last_test = MoyklassModel::getLessonVisitLastTest($userMk['id']);

        //$lesson = new Lesson();
        //$lesson_last_test = $lesson->getLessonsWithRecordsByUserId(['userId' => $userMk['id'], 'test' => 1], 'date DESC', 1);

        if (isset($lesson_last_test) and !empty($lesson_last_test)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last_test['date']));
            $this->DataUser['date_last_test_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $this->DataUser['date_last_test_lesson'] = '01.01.1970';
        }

        // Дата последнего посещения урока
        $lesson_last = MoyklassModel::getLessonVisitLast($userMk['id']);
        //$lesson_last = $lesson->getLessonsWithRecordsByUserId(['userId' => $userMk['id'], 'visit' => 1], 'date DESC', 1);
        if (isset($lesson_last) and !empty($lesson_last)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date']));
            $this->DataUser['date_last_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $this->DataUser['date_last_lesson'] = '01.01.1970';
        }

        // Дата последнего пропуска урока
        $lesson_last = MoyklassModel::getLessonSkipLast($userMk['id']);
        //$lesson_last = $lesson->getLessonsWithRecordsByUserId(['userId' => $userMk['id'], 'visit' => null], 'date DESC', 1);
        if (isset($lesson_last) and !empty($lesson_last)) {
            $date_last_skip_lesson = @date("d.m.Y", strtotime($lesson_last['date']));
            $this->DataUser['date_last_skip_lesson'] = $date_last_skip_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $this->DataUser['date_last_skip_lesson'] = '01.01.1970';
        }

        $lessonNextPaid = MoyklassModel::getNextPaidAndFreeRecording($userMk['id']);
        if (isset($lessonNextPaid) and !empty($lessonNextPaid)) {
            $this->DataUser['date_next_paid_lesson'] = $lessonNextPaid['date_next_paid_lesson'];
            $this->DataUser['date_next_free_lesson'] = $lessonNextPaid['date_next_free_lesson'];
        }



//        $lesson_last_paid = $lesson->getLessonsWithRecordsByUserId(['userId' => $userMk['id'], array('key' => 'date', 'val' => date('Y-m-d'), 'op' => Model::OP_GT)], 'date ASC', 1);
//        if (isset($lesson_last_paid) and !empty($lesson_last_paid)) {
//            $date_next_paid_lesson = @date("d.m.Y", strtotime($lesson_last_paid['date']));
//            $this->DataUser['date_next_paid_lesson'] = $date_next_paid_lesson;
//        } else { // Если даты нет, ставим "пустое значение поля"
//            $this->DataUser['date_next_paid_lesson'] = '01.01.1970';
//        }
//
//        $lesson_last_free = $lesson->getLessonsWithRecordsByUserId(['userId' => $userMk['id'], 'free' => 1, array('key' => 'date', 'val' => date('Y-m-d'), 'op' => Model::OP_GT)], 'date ASC', 1);
//        if (isset($lesson_last_free) and !empty($lesson_last_free)) {
//            $date_next_free_lesson = @date("d.m.Y", strtotime($lesson_last_free['date']));
//            $this->DataUser['date_next_free_lesson'] = $date_next_free_lesson;
//        } else { // Если даты нет, ставим "пустое значение поля"
//            $this->DataUser['date_next_free_lesson'] = '01.01.1970';
//        }

        return $this;
    }

    // Обновляет количество абонементов
    public function updateUserSubscriptions($email){

        $SubscriptionModel = new SubscriptionsModel();
        $getCountSubscriptionsByEmail = $SubscriptionModel->getCountSubscriptionsByEmail($email);
        // Данные стали брать из базы
        //$userSubscription = new UserSubscription();
       // $getCountSubscriptionsByEmail = $userSubscription->getUserSubscriptionsFromEmail($email);

        if (!empty($getCountSubscriptionsByEmail)) {
            $this->DataUser['count_user_subscriptions'] = $getCountSubscriptionsByEmail['all']['itemCount'];
            $this->DataUser['user_subscriptions_left_visits'] = ($getCountSubscriptionsByEmail['all']['visitCount'] - $getCountSubscriptionsByEmail['all']['visitedCount']);
            $this->DataUser['user_subscriptions_left_visits_individual'] = ($getCountSubscriptionsByEmail['individual']['visitCount'] - $getCountSubscriptionsByEmail['individual']['visitedCount']);
            $this->DataUser['user_subscriptions_left_visits_group'] = ($getCountSubscriptionsByEmail['group']['visitCount'] - $getCountSubscriptionsByEmail['group']['visitedCount']);
        }else{
            $this->DataUser['count_user_subscriptions'] = 0;
            $this->DataUser['user_subscriptions_left_visits'] = 0;
            $this->DataUser['user_subscriptions_left_visits_individual'] = 0;
            $this->DataUser['user_subscriptions_left_visits_group'] = 0;
        }

        return $this;
    }

    public function updateUserSubscriptionsByUserIdMK($userId){
        $SubscriptionModel = new SubscriptionsModel();
        $getCountSubscriptionsByMkUid = $SubscriptionModel->getCountSubscriptionsByMkUid($userId);

        if (!empty($getCountSubscriptionsByMkUid)) {
            $this->DataUser['count_user_subscriptions'] = $getCountSubscriptionsByMkUid['all']['itemCount'];
            $this->DataUser['user_subscriptions_left_visits'] = ($getCountSubscriptionsByMkUid['all']['visitCount'] - $getCountSubscriptionsByMkUid['all']['visitedCount']);
            $this->DataUser['user_subscriptions_left_visits_individual'] = ($getCountSubscriptionsByMkUid['individual']['visitCount'] - $getCountSubscriptionsByMkUid['individual']['visitedCount']);
            $this->DataUser['user_subscriptions_left_visits_group'] = ($getCountSubscriptionsByMkUid['group']['visitCount'] - $getCountSubscriptionsByMkUid['group']['visitedCount']);
        }else{
            $this->DataUser['count_user_subscriptions'] = 0;
            $this->DataUser['user_subscriptions_left_visits'] = 0;
            $this->DataUser['user_subscriptions_left_visits_individual'] = 0;
            $this->DataUser['user_subscriptions_left_visits_group'] = 0;
        }

        return $this;

    }






}