<?php

namespace App\Http\Controllers;

use App\Mail\VerificateEmail;
use App\Models\Member;
use App\Models\MKUser;
use App\Models\User\User;
use App\Models\UserNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
                UserNotification::where('id', '=', $id)
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
}
