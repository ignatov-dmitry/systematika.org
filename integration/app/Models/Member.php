<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int|null $gk_uid
 * @property string|null $gk_uhash
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property int|null $mk_uid
 * @property int|null $order_id
 * @property int|null $uid
 * @property string|null $cost_money
 * @property string|null $offers
 * @property string|null $comment
 * @property int|null $access
 * @property int|null $historyload
 * @property int|null $historyfirstload
 * @property int|null $mk_manager_id
 * @property string|null $foto_url
 * @method static \Illuminate\Database\Eloquent\Builder|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereCostMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereFotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereGkUhash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereGkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereHistoryfirstload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereHistoryload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereMkManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereMkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereOffers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereUid($value)
 * @mixin \Eloquent
 */
class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
}
