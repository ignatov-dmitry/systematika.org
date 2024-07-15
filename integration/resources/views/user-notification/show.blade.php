@extends('layouts.app')
@section('content')
    <div class="container-md">
        <div class="w-full max-w-[877px] p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <a class="font-medium text-blue-600 dark:text-blue-500 hover:underline" href="{{ back()->getTargetUrl() }}">Назад</a>
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ $user->email}}</h5>

            </div>
            <form action="">
                <table class="table">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Контакт
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Тип
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Описание
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Активность
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input value="{{ request('contact') }}" type="text" name="contact" class="form-control me-2" placeholder="Контакт" required="">
                        </td>
                        <td>
                            <select name="type" class="form-control">
                                <option>Выберете тип</option>
                                @foreach(\App\Models\UserNotification::getContacts() as $key => $contact)
                                    <option value="{{ $key }}">{{ $contact }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input value="{{ request('comment') }}" type="text" name="comment" class="form-control me-2" placeholder="Описание" required="">
                        </td>
                        <td>
                            <input type="checkbox" name="is_checked" class="form-check-input">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
@endsection
