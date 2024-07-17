<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MKUser;
use App\Models\UserNotification;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

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
        // Нужно будет проверять при возникновении проблем на дубли

        $member = Member::where('gk_uhash', '=', $hash)->first();
        $user = MKUser::where('email', '=', $member->email)->first();

        $notifications = UserNotification::where('user_id', '=', $user->id)->get();
        return view('user-notification.show', compact('user', 'notifications'));
    }

    public function save(Request $request, MKUser $user)
    {
        UserNotification::where('user_id', '=', $user->id)->delete();

        foreach ($request->get('user_notifications') as $notification)
        {
            if ($notification['contact'] && $notification['type'])
                UserNotification::create([
                    'user_id'       => $user->id,
                    'contact'       => $notification['contact'],
                    'type'          => $notification['type'],
                    'comment'       => $notification['comment'],
                    'is_checked'    => $notification['is_checked'] ?? 0
                ]);
        }

        return redirect()->back();
    }
}
