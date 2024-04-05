<?php


namespace GKTOMK\Models\Systematika\MoyKlass;


use GKTOMK\Models\DB;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\Util;

class User extends Model
{
    protected string $tableName = 'mk_users';

    public function updateRecord($data)
    {
        $sql = "
            UPDATE {table} 
            SET 
                name = '{name}', 
                balans = '{balans}', 
                email = '{email}', 
                advSourceId = '{advSourceId}', 
                phone = '{phone}'
            WHERE id = {id}
        ";

        $sql = Util::replaceTokens($sql, array(
            'table'                 => $this->getTableName(),
            'id'                    => $data['userId'],
            'name'                  => $data['name'] ?: 'NULL',
            'balans'                => $data['balans'] ?: 0,
            'email'                 => $data['email'] ?: '',
            //'statusId'              => $data['statusId'] ?: 'NULL',
            'advSourceId'           => $data['advSourceId'] ?: 0,
            //'statusReasonId'        => $data['createSourceId'] ?: 'NULL',
            //'prevStatusId'          => $data['prevStatusId'] ?: 'NULL',
            //'prevStatusReasonId'    => $data['prevStatusReasonId'] ?: 'NULL',
            'phone'                 => $data['phone'] ?: '',
            //'remind'                => $data['remind'] ?: 'NULL',
        ));

        DB::exec($sql);
    }
}