@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="w-full max-w-[877px] p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="{{ back()->getTargetUrl() }}">Назад</a>
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ $info['user']['email'] }}</h5>
                @if(isset($log->mk_user->id))
                    <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="https://app.moyklass.com/user/{{ $log->mk_user->id }}/joins" target="_blank">Мой класс</a>
                @endif
                @if(isset($log->integration_user->gk_uid))
                    <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="https://online.systematika.org/user/control/user/update/id/{{ $log->integration_user->gk_uid }}" target="_blank">Getcource</a>
                @endif
                <p class="font-bold leading-none text-gray-900 dark:text-white">Обновление ({{ $log->date_create->format('d.m.Y H:i:s') }})</p>
            </div>
            <div class="flow-root">
                <ul role="list" class="list-group">
                    @foreach($info['user']['addfields'] as $key => $value)
                        <li class="list-group-item py-3 sm:py-4">
                            <div class="flex items-center">
                                <div class="flex-1 min-w-0 ms-4">
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                        {{ $key }}: {{ $value }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
