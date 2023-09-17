<?php


namespace GKTOMK\Models\Systematika\MoyKlass;


use GKTOMK\Models\DB;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\Util;

class LessonRecord extends Model
{
    protected string $tableName = 'mk_lesson_records';

    public function getRecordsWithUsers(int $lessonId, $date)
    {
        $sql = "
            SELECT uT.id AS user_id,
            uT.email AS email,
            uT.phone AS phone,
            lRT.lessonId AS lesson_id,
            lRT.id AS record_id,
            clT.id AS class_id,
            coT.name AS course_name,
            clT.name AS class_name,
            UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(lT.date, '|', lT.beginTime), '%Y-%m-%d|%H:%i:%s')) timestart,
            lT.beginTime AS begin_time,
            uT.name AS full_name,
            lT.topic AS topic,
            (SELECT w.status FROM whatsappmessages AS w WHERE w.record_id_mk = lRT.id ORDER by w.date DESC LIMIT 1) `whatsapp_status`

            FROM {table} AS lRT
                LEFT JOIN {lessonTable} AS lT ON lT.id = lRT.lessonId
                LEFT JOIN {userTable} AS uT ON uT.id = lRT.userId
                LEFT JOIN {classTable} AS clT ON clT.id = lT.classId
                LEFT JOIN {courseTable} AS coT ON coT.id = clT.courseId
            WHERE lRT.lessonId = {lessonId} and uT.id is not null
            HAVING `whatsapp_status` IS NULL or `whatsapp_status` <> 'sent'
            ORDER BY lT.beginTime;
        ";

        $sql = Util::replaceTokens($sql, array(
            'whatsappTable' => 'whatsappmessages',
            'table' => $this->getTableName(),
            'lessonTable' => Lesson::getInstance()->getTableName(),
            'userTable' => User::getInstance()->getTableName(),
            'classTable' => Classes::getInstance()->getTableName(),
            'courseTable' => Course::getInstance()->getTableName(),
            'lessonId' => $lessonId
        ));

        return $this->getAll($sql);
    }

    public function updateRecord($data)
    {
        $sql = "
            UPDATE {table} SET free = {free}, skip = {skip}, visit = {visit}, goodReason = {goodReason}, test = {test}, paid = {paid}
            WHERE id = {id} AND userId = {userId}
        ";

        $sql = Util::replaceTokens($sql, array(
            'table'         => $this->getTableName(),
            'id'            => $data['id'],
            'free'          => $data['free'] ?: 'NULL',
            'test'          => $data['test'] ?: 'NULL',
            'skip'          => $data['skip'] ?: 'NULL',
            'visit'         => $data['visit'] ?: 'NULL',
            'goodReason'    => $data['goodReason'] ?: 'NULL',
            'paid'          => $data['paid'] ?: 'NULL',
            'userId'        => $data['userId'] ?: 'NULL'
        ));

        DB::exec($sql);
    }

    public function addLessonRecord($data)
    {
        $date = new \DateTime($data['createdAt']);

        $sql = Model::getInstance()->prepareBulkInsert($this->getTableName(),
            ['id', 'free', 'test', 'skip', 'visit', 'userId', 'lessonId', 'createdAt', 'goodReason'],
            [[
                $data['lessonRecordId'],
                $data['free'],
                $data['test'],
                $data['skip'],
                $data['visit'],
                $data['userId'],
                $data['lessonId'],
                $date->format('Y-m-d H:i:s'),
                $data['goodReason'],
            ]]);

        DB::exec($sql);
    }
}