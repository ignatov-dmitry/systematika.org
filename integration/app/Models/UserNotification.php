<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

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
