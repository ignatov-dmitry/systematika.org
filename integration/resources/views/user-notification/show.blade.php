@php use App\Models\UserNotification; @endphp
@extends('layouts.app_get_cource')
@section('content')
    <h1 class="mt-3 text-center">Уведомление о начале занятия</h1>
    <div class="container-md">
        <div
            class="w-full max-w-[877px] p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ $member->email}}</h5>
            </div>
            <div class="row">
                <div class="col-2"><button class="btn btn-info">Подключить email</button></div>
                <div class="col-2"><button class="btn btn-info">Подключить Whatsapp</button></div>
                <div class="col-2"><button class="btn btn-info">Подключить Телеграм</button></div>
                <div class="col-2"><button class="btn btn-info">Подключить ВК</button></div>
            </div>
            <form action="{{ route('user-notification.save', $member) }}" method="post">
                @method('POST')
                @csrf
                <table class="table" id="contacts">
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
                        <th scope="col" class="px-6 py-3">
                            Код подтверждения
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($member->email)
                        <tr>
                            <td>
                                <input disabled value="{{ $member->email }}" type="text" class="form-control me-2">
                            </td>
                            <td>
                                <input disabled value="email" type="text" class="form-control me-2">
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                    @if($member->phone)
                        <tr>
                            <td>
                                <input disabled value="{{ $member->phone }}" type="text" class="form-control me-2">
                            </td>
                            <td>
                                <input disabled value="email" type="text" class="form-control me-2">
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif
                    @if($notifications)
                        @php $row = 0 @endphp
                        @foreach($notifications as $notification)
                            <tr>
                                <td>
                                    <input disabled value="{{ request('contact') ?? $notification->contact }}" type="text"
                                           name="user_notifications[{{ 'id_' . $notification->id }}][contact]" class="form-control me-2"
                                           placeholder="Контакт" required>
                                </td>
                                <td>
                                    <select disabled name="user_notifications[{{ 'id_' . $notification->id }}][type]" class="form-control" required>
                                        <option>Выберете тип</option>
                                        @foreach(UserNotification::getContacts() as $key => $contact)
                                            <option @if($notification->type == $key) selected
                                                    @endif value="{{ $key }}">{{ $contact }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input value="{{ request('comment') ?? $notification->comment }}" type="text"
                                           name="user_notifications[{{ 'id_' . $notification->id }}][comment]" class="form-control me-2"
                                           placeholder="Описание">
                                </td>
                                <td>
                                    <input @if($notification->is_checked == 1) checked @endif type="checkbox" value="1" name="user_notifications[{{ 'id_' . $notification->id }}][is_checked]"
                                           class="form-check-input">
                                </td>
                                <td>
                                    <input readonly type="text" value="1"
                                           class="form-control">
                                </td>
                            </tr>
                            @php $row++ @endphp
                        @endforeach
                    @endif
                    </tbody>
                </table>
{{--                <button type="button" id="addRowBtn" class="btn btn-primary">Добавить строку</button>--}}
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
    <script>

    </script>
@endsection
