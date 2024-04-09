<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
 * @property mixed $password
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalans($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDateAdd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDateUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkCostMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkOffers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGkUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatusResult($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
