<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $contact
 * @property string $type
 * @property string|null $comment
 * @property int $is_checked
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereIsChecked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereUserId($value)
 * @property string|null $request_code
 * @property int $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNotification whereRequestCode($value)
 * @mixin \Eloquent
 */
class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact',
        'type',
        'comment',
        'is_checked',
        'request_code',
        'is_active'
    ];
    public $timestamps = false;
    public const EMAIL = 'EMAIL';
    public const WHATSAPP = 'WHATSAPP';
    public const VK = 'VK';
    public const TELEGRAM = 'TELEGRAM';

    private static array $contacts = [
        self::EMAIL     => 'Email',
        self::WHATSAPP  => 'Whatsapp',
        self::VK        => 'Вконтакте',
        self::TELEGRAM  => 'Telegram',
    ];

    public static function getContacts(): array
    {
        return self::$contacts;
    }

    public static function getContact($slug): bool
    {
        return self::$contacts[$slug] ?? false ;
    }
}
