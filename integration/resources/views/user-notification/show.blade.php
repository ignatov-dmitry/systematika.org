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
                <div class="col-2"><button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#emailModal">Подключить email</button></div>
                <div class="col-2"><button class="btn btn-info">Подключить Whatsapp</button></div>
                <div class="col-2"><button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#telegramModal">Подключить Telegram</button></div>
                <div class="col-2"><button class="btn btn-info">Подключить ВК</button></div>
            </div>
            <form action="{{ route('user-notification.save', $member->gk_uhash) }}" method="post">
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
                                    <input style="display: none;" checked type="checkbox" value="0" name="user_notifications[{{ 'id_' . $notification->id }}][is_checked]"
                                           class="form-check-input">
                                    <input @if($notification->is_active == 1) checked @endif type="checkbox" value="1" name="user_notifications[{{ 'id_' . $notification->id }}][is_checked]"
                                           class="form-check-input">
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
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Подключить почту</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user-notification.sendCodeForEmail', $member) }}" id="emailForm">
                        @method('POST')
                        @csrf
                        <input class="form-control" type="text" name="email" placeholder="email">
                        <br>
                        <button class="btn btn-primary" id="sendEmailCheck">Отправить проверочный код</button>
                        <p style="color: #198754;" id="sendMessageSuccessText"></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeEmailModal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="telegramModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Подключить телеграм</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('user-notification.telegramSubscribe', $member) }}" id="telegramForm">
                        @method('POST')
                        @csrf
                        <input required class="form-control" type="text" name="token" placeholder="Код из телеграм бота">
                        <br>
                        <button class="btn btn-primary" id="sendTelegramCheck">Отправить проверочный код</button>
                        <a target="_blank" href="https://t.me/SystematikaNotifybot?start" class="btn btn-info">Получить код авторизации</a>
                        <p style="color: #198754;" id="sendMessageSuccessText"></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeEmailModal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('emailForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting via the browser

            const formData = new FormData(this);

            const formObject = {};
            formData.forEach((value, key) => {
                formObject[key] = value;
            });

            window.axios.post('{{ route('user-notification.sendCodeForEmail', $member) }}', formObject)
                .then(function (response) {
                    alert('Ссылка на активацию отправлена вам на почту');
                    document.getElementById('sendMessageSuccessText').textContent = 'Ссылка для активации отправлена вам на почту!';
                    $('#emailModal #closeEmailModal').click();
                })
                .catch(function (error) {
                    alert(error.response.data.message);
                });
        });
    </script>
@endsection
