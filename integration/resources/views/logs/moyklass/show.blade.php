@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4">
        <div class="relative overflow-x-auto">
            <table class="table">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        MoyKlass ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Имя
                    </th>
                    <th scope="col" class="px-6 py-3">
                        email
                    </th>
                    <th scope="col" class="px-6 py-3">

                    </th>

                </tr>
                </thead>
                <tbody>
                @foreach($data['users'] as $user)
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="https://app.moyklass.com/user/{{ $user->id }}/joins" target="_blank">{{ $user->id }}</a>
                        </th>
                        <td class="px-6 py-4">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4">

                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
            <div class="columns-2" style="display: none;">
                <div class="flex font-sans">
                    <div class="flex flex-wrap">
                        <h1 class="flex-auto text-lg font-semibold text-gray-300">
                            Классическая куртка в стиле милитари
                        </h1>
                        <div class="text-lg font-semibold text-gray-300">
                            $110.00
                        </div>
                        <div class="w-full flex-none text-sm font-medium text-gray-300 mt-2">
                            In stock
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
