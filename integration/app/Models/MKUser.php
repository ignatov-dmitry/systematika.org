<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $updatedAt
 * @property string|null $createdAt
 * @property float|null $balans
 * @property int|null $advSourceId
 * @property int|null $createSourceId
 * @property string|null $stateChangedAt
 * @property int|null $clientStateId
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereAdvSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereBalans($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereClientStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereCreateSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereStateChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MKUser whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MKUser extends Model
{
    use HasFactory;

    protected $table = 'mk_users';
}
