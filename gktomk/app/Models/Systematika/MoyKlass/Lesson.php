<?php


namespace GKTOMK\Models\Systematika\MoyKlass;


use GKTOMK\Models\DB;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\Util;

class Lesson extends Model
{
    protected string $tableName = 'mk_lessons';

    public function getLessonsWithRecordsByUserId($criteria = array(), $orderBy = null, $limit = null): ?array
    {
        $whereCondition = '';
        $sql = '
            SELECT ml.id, ml.date, mlr.*
            FROM {ml} AS ml
                LEFT JOIN {mlr} AS mlr on ml.id = mlr.lessonId
            WHERE {where}
        ';

        if ($orderBy) $sql .= ' ORDER BY {order}';
        if ($limit) $sql .= ' LIMIT {limit}';

        if ($criteria){
            $whereCondition = $this->prepareWhere($criteria);
        }

        $sql = Util::replaceTokens($sql, array(
            'ml'    => $this->getTableName(),
            'mlr'   => (new LessonRecord())->getTableName(),
            'where' => $whereCondition,
            'order' => $orderBy,
            'limit' => $limit
        ));

        if ($limit == 1)
            return DB::getRow($sql);
        else
            return DB::getAll($sql);
    }

    public function getLessonsWithRecordsByAllUsers()
    {
        $sql = '
            SELECT mu.email,
                   IFNULL((SELECT ml.date FROM mk_lessons as ml LEFT JOIN mk_lesson_records AS mlr on ml.id = mlr.lessonId WHERE mlr.userId = mu.id AND mlr.visit = 1 AND mlr.free = 1 ORDER BY date desc LIMIT 1), \'1970-01-01\') as lastTestLesson,
                   IFNULL((SELECT ml.date FROM mk_lessons as ml LEFT JOIN mk_lesson_records AS mlr on ml.id = mlr.lessonId WHERE mlr.userId = mu.id AND mlr.visit = 1 ORDER BY date desc LIMIT 1), \'1970-01-01\') as lastLesson,
                   IFNULL((SELECT ml.date FROM mk_lessons as ml LEFT JOIN mk_lesson_records AS mlr on ml.id = mlr.lessonId WHERE mlr.userId = mu.id AND mlr.skip = 1 AND date <= \'{currentDate}\' ORDER BY date desc LIMIT 1), \'1970-01-01\') as lastSkipLesson,
                   IFNULL((SELECT ml.date FROM mk_lessons as ml LEFT JOIN mk_lesson_records AS mlr on ml.id = mlr.lessonId WHERE mlr.userId = mu.id AND mlr.free = 0 AND date >= \'{currentDate}\' ORDER BY date LIMIT 1), \'1970-01-01\') as nextPaidLesson,
                   IFNULL((SELECT ml.date FROM mk_lessons as ml LEFT JOIN mk_lesson_records AS mlr on ml.id = mlr.lessonId WHERE mlr.userId = mu.id AND mlr.free = 1 AND date >= \'{currentDate}\' ORDER BY date LIMIT 1), \'1970-01-01\') as nextFreeLesson
            FROM {userTable} AS mu WHERE mu.email IS NOT NULL;
        ';

        $sql = Util::replaceTokens($sql, [
            'userTable'   => User::getInstance()->getTableName(),
            'currentDate' => date('Y-m-d')
        ]);


        return DB::getAll($sql);
    }
}