<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $token
 * @property string $chat_id
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramToken whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TelegramToken whereToken($value)
 * @mixin \Eloquent
 */
class TelegramToken extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
}
