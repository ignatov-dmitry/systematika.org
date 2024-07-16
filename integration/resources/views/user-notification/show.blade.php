@php use App\Models\UserNotification; @endphp
@extends('layouts.app')
@section('content')
    <div class="container-md">
        <div
            class="w-full max-w-[877px] p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">{{ $user->email}}</h5>

            </div>
            <form action="{{ route('user-notification.save', $user) }}" method="post">
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
                    </tr>
                    </thead>
                    <tbody>
                    @if($notifications)
                        @php $row = 0 @endphp
                        @foreach($notifications as $notification)
                            <tr>
                                <td>
                                    <input value="{{ request('contact') ?? $notification->contact }}" type="text"
                                           name="user_notifications[{{ $row }}][contact]" class="form-control me-2"
                                           placeholder="Контакт" required>
                                </td>
                                <td>
                                    <select name="user_notifications[{{ $row }}][type]" class="form-control" required>
                                        <option>Выберете тип</option>
                                        @foreach(UserNotification::getContacts() as $key => $contact)
                                            <option @if($notification->type == $key) selected
                                                    @endif value="{{ $key }}">{{ $contact }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input value="{{ request('comment') ?? $notification->comment }}" type="text"
                                           name="user_notifications[{{ $row }}][comment]" class="form-control me-2"
                                           placeholder="Описание">
                                </td>
                                <td>
                                    <input @if($notification->is_checked == 1) checked @endif type="checkbox" value="1" name="user_notifications[{{ $row }}][is_checked]"
                                           class="form-check-input">
                                </td>
                            </tr>
                            @php $row++ @endphp
                        @endforeach
                    @endif
                    </tbody>
                </table>
                <button type="button" id="addRowBtn" class="btn btn-primary">Добавить строку</button>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
    <script>
        const tableBody = document.getElementById('contacts').getElementsByTagName('tbody')[0];
        let rowCount = tableBody.rows.length;
        document.getElementById('addRowBtn').addEventListener('click', function () {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input value="{{ request('contact') }}" type="text" name="user_notifications[${rowCount + 1}][contact]" class="form-control me-2" placeholder="Контакт">
                </td>
                <td>
                    <select name="user_notifications[${rowCount + 1}][type]" class="form-control">
                        @foreach(UserNotification::getContacts() as $key => $contact)
            <option value="{{ $key }}">{{ $contact }}</option>
                        @endforeach
            </select>
        </td>
        <td>
            <input value="{{ request('comment') }}" type="text" name="user_notifications[${rowCount + 1}][comment]" class="form-control me-2" placeholder="Описание">
                </td>
                <td>
                    <input type="checkbox" name="user_notifications[${rowCount + 1}][is_checked]" value="1" class="form-check-input">
                </td>
            `;

            tableBody.appendChild(newRow);
            rowCount++;
        });
    </script>
@endsection
