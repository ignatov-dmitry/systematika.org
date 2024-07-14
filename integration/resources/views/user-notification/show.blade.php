@extends('layouts.app')
@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-[877px] p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="{{ back()->getTargetUrl() }}">Назад</a>
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ $user->email}}</h5>

            </div>
            <div class="flow-root">
                <label class="dark:text-white" for="email">Email</label>
                <input id="email" type="text" class="bg-gray-50 border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 border dark:focus:border-blue-500">
            </div>
        </div>
    </div>
@endsection
