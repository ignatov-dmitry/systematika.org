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
            <nav class="navbar navbar-expand-lg navbar-light bg-light" aria-label="Page navigation example">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="?date={{ $nextDay }}" class="nav-link">{{ $nextDay }}</a>
                    </li>
                    <li class="nav-item">
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <input value="{{ request('date') ?? $currDay }}" type="date" name="date" class="form-control me-2" placeholder="John" required="">
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="?date={{ $prevDay }}" class="nav-link">{{ $prevDay }}</a>
                    </li>
                </ul>
            </nav>
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <input value="{{ request('email') }}" type="email" name="email" class="form-control me-2" placeholder="Найти по email" required="">
                </div>
            </div>
            <input type="submit" value="" style="display: none;">
        </form>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3">
                            MK
                        </th>
                        <th scope="col" class="px-6 py-3">
                            GK
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
                                @if(isset($log->mk_user->id))
                                    <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="https://app.moyklass.com/user/{{ $log->mk_user->id }}/joins" target="_blank">Мой класс</a>
                                @else
                                    Пользователь не найден в базе данных
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(isset($log->integration_user->gk_uid))
                                    <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="https://online.systematika.org/user/control/user/update/id/{{ $log->integration_user->gk_uid }}" target="_blank">Getcource</a>
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

            {{ $logs->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
