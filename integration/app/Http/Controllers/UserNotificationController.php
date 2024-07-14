<?php

namespace App\Http\Controllers;

use App\Models\MKUser;
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

    public function info(MKUser $user): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('user-notification.show', compact('user'));
    }
}
