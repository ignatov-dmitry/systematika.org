@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4">
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
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
        </div>
    </div>
@endsection
