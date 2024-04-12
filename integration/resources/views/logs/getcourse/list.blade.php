@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            MK
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Дата выполнения
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Время выполнения
                        </th>
                        <th scope="col" class="px-6 py-3">

                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $log->id }}
                            </th>
                            <td class="px-6 py-4">
                                @if(isset($log->user->id))
                                    <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="https://app.moyklass.com/user/{{ $log->user->id }}/joins" target="_blank">Перейти</a>
                                @else
                                    Пользователь не найден в базе данных
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->email }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->date_create->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->date_create->format('H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('getcource.info', $log) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Информация</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $logs->links('vendor.pagination.default') }}
        </div>
    </div>
@endsection
