<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property string $email
 * @property string $request
 * @property int $date_create
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog whereDateCreate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GKUpdateLog whereRequest($value)
 * @mixin \Eloquent
 */
class GKUpdateLog extends Model
{
    use HasFactory;

    protected $table = 'loggk';

    public function getDates()
    {
        return [
            'date_create'
        ];
    }
}
