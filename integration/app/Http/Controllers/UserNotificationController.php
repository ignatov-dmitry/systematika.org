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

    public function save(Request $request, MKUser $user): RedirectResponse
    {
        foreach ($request->get('user_notifications') as $key => $notification)
        {
            if (str_contains($key, 'id_'))
            {
                $id = (explode('_', $key))[1];
                UserNotification::where('id', '=', $id)
                    ->update([
                        'comment'       => $notification['comment'],
                        'is_active'    => $notification['is_checked'] ?? 0,
                ]);
            }
        }

        return redirect()->back();
    }

    public function sendCodeForEmail(Request $request, string $hash): JsonResponse
    {
        $code = rand(111111, 999999);
        $email = $request->get('email');
        Mail::to($email)->send(new VerificateEmail($code));

        $member = Member::where('gk_uhash', '=', $hash)->first();

        UserNotification::create([
            'user_id'       => $member->id,
            'contact'       => $email,
            'type'          => UserNotification::EMAIL,
            'request_code'  => $code
        ]);

        return response()->json(['status' => 'OK']);
    }

    public function verifyEmail(Request $request, string $hash)
    {
        $email = $request->get('email');
        $code = $request->get('code');
        $member = Member::where('gk_uhash', '=', $hash)->first();

        $userNotification = UserNotification::where('user_id', '=', $member->id)
            ->where('contact', '=', $email)->first();

        if ($code == $userNotification->request_code)
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
