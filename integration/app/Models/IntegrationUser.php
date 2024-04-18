<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int|null $gk_uid
 * @property string|null $gk_first_name
 * @property string|null $gk_last_name
 * @property string|null $gk_email
 * @property string|null $gk_phone
 * @property string|null $gk_cost_money
 * @property string|null $gk_offers
 * @property int|null $date_add
 * @property int|null $date_update
 * @property string|null $status
 * @property string|null $status_result
 * @property string|null $gk_comment
 * @property string|null $balans
 * @property int|null $gk_order
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereBalans($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereDateAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereDateUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkCostMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkOffers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereGkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationUser whereStatusResult($value)
 * @mixin \Eloquent
 */
class IntegrationUser extends Model
{
    use HasFactory;

    protected $table = 'users';
}
