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
            <div class="grid gap-6 mb-6 md:grid-cols-2">
                <div>
                    <input value="{{ request('email') }}" type="email" name="email" class="bg-gray-50 border-gray-300 text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 border dark:focus:border-blue-500" placeholder="Найти по email" required="">
                </div>
            </div>
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
                            Имя
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3">

                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $user->id }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('user-notification.info', $user) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Перейти</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links('vendor.pagination.default') }}
        </div>
    </div>
@endsection
