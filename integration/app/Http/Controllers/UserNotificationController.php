<?php

namespace App\Http\Controllers;

use App\Mail\VerificateEmail;
use App\Models\Member;
use App\Models\MKUser;
use App\Models\TelegramToken;
use App\Models\UserNotification;
use App\Notifications\AlreadySubscribed;
use App\Notifications\CustomTelegramMessage;
use App\Notifications\SubscribeToBot;
use App\Services\Wazzup;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class UserNotificationController extends Controller
{
    public function list(Request $request): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $users = MKUser::query();

        if ($email = $request->get('email'))
            $users->where('email', '=', $email);

        $users = $users
            ->paginate(25)
            ->withQueryString();

        return view('user-notification.list', compact('users'));
    }

    public function info($hash): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $member = Member::where('gk_uhash', '=', $hash)->first();

        $notifications = UserNotification::where('user_id', '=', $member->id)->get();
        return view('user-notification.show', compact('member', 'notifications'));
    }

    public function save(Request $request, string $hash): RedirectResponse
    {
        $member = Member::where('gk_uhash', '=', $hash)->first();
        foreach ($request->get('user_notifications') as $key => $notification)
        {
            if (str_contains($key, 'id_'))
            {

                $id = (explode('_', $key))[1];
                UserNotification::where('id', '=', (int)$id)
                    ->where('user_id', '=', $member->id)
                    ->update([
                        'comment'   => $notification['comment'],
                        'is_active' => $notification['is_checked'] ?? 0,
                ]);
            }
        }

        return redirect()->back();
    }

    public function sendCodeForEmail(Request $request, Member $member): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user_notifications,contact'],
        ]);
        $code = md5(rand(111111, 999999));
        $email = $request->get('email');
        Mail::to($email)->send(new VerificateEmail($code, $member->gk_uhash, $email));

        $member = Member::where('id', '=', $member->id)->first();

        $userNotification = UserNotification::where('user_id', '=', $member->id)
            ->where('contact', '=', $email)
            ->where('is_checked', '=', 1)
            ->first();

        if ($userNotification)
            return response()->json(['status' => 'Почта уже подтверждена']);

        else
            UserNotification::updateOrCreate([
                'user_id'       => $member->id,
                'contact'       => $email,
            ],[
                'user_id'       => $member->id,
                'contact'       => $email,
                'type'          => UserNotification::EMAIL,
                'request_code'  => $code,
                'is_checked'    => 0
            ]);

        return response()->json(['status' => 'Ссылка отправлена']);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $code = $request->get('request_code');
        $hash = $request->get('hash');
        $member = Member::where('gk_uhash', '=', $hash)->first();

        $userNotification = UserNotification::where('user_id', '=', $member->id)
            ->where('contact', '=', $email)->first();


        if ($code == @$userNotification->request_code && isset($userNotification->request_code))
        {
            $userNotification->request_code = null;
            $userNotification->is_checked = 1;
            $userNotification->save();

            return response()->json(['status' => 'verified']);
        }

        else
            return response()->json(['status' => 'Wrong code']);
    }

    public function telegramStart(Request $request)
    {
        $text = false;
        $chatId = false;

        $updates = $request->all();
        Log::debug('telegram-get-updates', $updates);

        if (!empty($updates['message']['chat']['id'])) {
            // Chat ID
            $chatId = $updates['message']['chat']['id'];
            $text = strtolower(trim($updates['message']['text']));
        }

        if ($text == '/start' || $text == 'start') {
            $user = UserNotification::where('contact', '=', $chatId)->first();
            if ($user) {
                Notification::route('telegram', $chatId)->notify(new AlreadySubscribed());
                return 'OK';
            }

            $token = rand(100000, 9999999);
            $tgToken = TelegramToken::where('chat_id', $chatId)->first();
            if ($tgToken !== null) {
                $token = $tgToken->token;
            }

            $tgToken = new TelegramToken();
            $tgToken->token = $token;
            $tgToken->chat_id = $chatId;
            $tgToken->save();

            Notification::route('telegram', $chatId)
                ->notify(new SubscribeToBot($token));
        }

        return 'OK';
    }

    public function telegramSubscribe(Request $request, Member $member): JsonResponse
    {
        $token = $request->get('token');
        $tgToken = TelegramToken::where('token', $token)->first();

        $userNotification = UserNotification::where('user_id', '=', $member->id)
            ->where('contact', '=', $tgToken->chat_id)->first();

        if ($tgToken !== null && !$userNotification) {
            $notification = UserNotification::create([
                'user_id'       => $member->id,
                'type'          => UserNotification::TELEGRAM,
                'contact'       => $tgToken->chat_id,
                'is_checked'    => 1
            ]);


            $tgToken->delete();
            Notification::route('telegram', $notification->contact)
                ->notify(new CustomTelegramMessage(
                    'Приветствуем, ' . $member->first_name . '! Теперь ты будешь получать уведомления о всех важных событиях :)'
                ));

            return response()->json(['status' => 'verified']);
        }
        else
            return response()->json(['status' => 'Wrong code']);
    }

    public function sendWhatsappCode(Request $request, Member $member)
    {
        $data = [
            'channelId' => 'a7d9355f-4d4b-452e-ad7d-d1348f64ea5f',
            'chatType' => 'whatsapp',
            'chatId' => $request->get('phone')
        ];

        $notification = UserNotification::where('contact', '=', $request->get('phone'))
            ->first();

        if (@$notification->is_checked == 1)
            return response()->json(['status' => 'Ваш номер уже есть в системе']);
        else
        {
            $token = rand(1000, 9999);
            $data['text'] = 'Ваш код: ' . $token;

            UserNotification::updateOrCreate(
                [
                    'user_id'       => $member->id,
                    'contact'       => $request->get('phone')
                ],
                [
                    'type'          => UserNotification::WHATSAPP,
                    'is_checked'    => 0,
                    'request_code'  => $token
                ]);
        }
        Wazzup::sendMessage($data);
        return response()->json(['status' => 'Код отправлен вам в whatsapp']);
    }

    public function checkWhatsappCode(Request $request, Member $member): JsonResponse
    {
        $user = UserNotification::where('contact', '=', $request->get('phone'))
            ->where('user_id', '=', $member->id)
            ->first();

        if ($user->request_code == $request->get('token'))
        {
            $user->is_checked = 1;
            $user->request_code = null;
            $user->save();

            return response()->json(['status' => 'Номер привязан']);
        }

        return response()->json(['status' => 'Wrong code']);
    }

    protected function getSubscribeUrl(string $token): string
    {
        return route('user-notification.telegramSubscribe', ['token' => $token], true);
    }
}
