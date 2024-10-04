@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <a class="btn btn-info" href="?status=loaded">Обработанные хуки</a>
            <a class="btn btn-info" href="?status=new">Необработанные хуки</a>
            <a class="btn btn-info" href="?status=processing">Хуки в обработке</a>
            <a class="btn btn-info" href="?status=fail">Хуки с ошибками</a>
            @if(count($logs))
                <table class="table">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Название хука
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Время инициализации
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Время выполнения
                        </th>
                        <th>
                            Статус
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
                                {{ $log->event }}
                            </td>
                            <td class="px-6 py-4">
                                {{ date('d.m.Y H:i:s', (json_decode($log->request, true))['init']['time']) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->date_loaded?->format('d.m.Y') }} {{ $log->date_loaded?->format('H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->status }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('moyklass.info', $log) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Перейти</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="center">
                    Записей нет
                </div>
            @endif

            {{ $logs->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
