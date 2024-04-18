@php
use Illuminate\Support\Carbon;

$prevDay = Carbon::parse(request('date'))->subDay()->format('Y-m-d');
$currDay = Carbon::parse(request('date'))->format('Y-m-d');
$nextDay = Carbon::parse(request('date'))->addDay()->format('Y-m-d');

@endphp
@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4">
        <form>
            <nav class="flex justify-center m-5" aria-label="Page navigation example">
                <ul class="inline-flex -space-x-px text-base h-10">
                    <li>
                        <a href="?date={{ $prevDay }}" class="flex items-center justify-center px-4 h-10 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>
                    </li>
                    <li>
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <input value="{{ request('date') ?? $currDay }}" type="date" name="date" class="bg-gray-50 border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="John" required="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="?date={{ $nextDay }}" class="flex items-center justify-center px-4 h-10 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>
                    </li>
                </ul>
            </nav>
            <input type="submit" value="" class="hidden">
        </form>
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
