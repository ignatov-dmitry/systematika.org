@php use App\Models\MKWebhookLog; @endphp
@extends('layouts.app')
@section('content')
    <div class="container mx-auto px-4 my-2">
        <div class="row gy-5">
            <form>
                <div class="mb-3">
                    <input name="search" class="form-control mb-1" type="text" value="{{ request()->get('search') }}" placeholder="Поиск по EMAIL, GetCourseID, Имени и Фамилии">
                    <button class="btn btn-info" type="submit">Поиск</button>
                </div>
            </form>
        </div>
        <div class="accordion my-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Статистика
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Количество</th>
                                <th>Макс. время</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><strong>Необработанные</strong></td>
                                <td><strong>{{ $newCount }}</strong></td>
                                <td><strong>{{ formatSecondsToTime($logWithMaxDifference->difference ?? 0) }}</strong></td>
                            </tr>
                            @foreach($logWithMaxDifferenceForWebhooks as $webhook)
                                <tr>
                                    <td>
                                        <p>{{ MKWebhookLog::getEventName($webhook->event) }} (<span class="small">{{ $webhook->event }}</span>)</p>
                                    </td>
                                    <td><strong>{{ $webhook->event_count }}</strong></td>
                                    <td><strong>{{ formatSecondsToTime($webhook->difference) }}</strong></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td><strong>В обработке</strong></td>
                                <td><strong>{{ $progressCount }}</strong></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>С ошибками</strong></td>
                                <td><strong>{{ $failCount }}</strong></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>Событие</th>
                                <th>Вебхуки за 7 дней</th>
                                <th>Вебхуки за сегодня</th>
                                <th>Вебхуки за вчера</th>
                                <th>2 дня назад</th>
                                <th>3 дня назад</th>
                                <th>4 дня назад</th>
                                <th>5 дней назад</th>
                                <th>6 дней назад</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($logWithMaxDifferenceForWebhooksWeek as $log)
                                <tr>
                                    <td>{{ MKWebhookLog::getEventName($log->event) }} (<span class="small">{{ $log->event }}</span>)</td>
                                    <td>{{ $log->count_last_7_days }}</td>
                                    <td>{{ $log->count_today }}</td>
                                    <td>{{ $log->count_yesterday }}</td>
                                    <td>{{ $log->count_day_2 }}</td>
                                    <td>{{ $log->count_day_3 }}</td>
                                    <td>{{ $log->count_day_4 }}</td>
                                    <td>{{ $log->count_day_5 }}</td>
                                    <td>{{ $log->count_day_6 }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                            Время прихода на сервер
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
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $log->id }}
                            </th>
                            <td class="px-6 py-4">
                                {{ MKWebhookLog::getEventName($log->event) }} (<span class="small">{{ $log->event }}</span>) [{{ $log->priority }}]
                            </td>
                            <td class="px-6 py-4">
                                {{ formatCustomDate((json_decode($log->request, true))['init']['time']) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formatCustomDate($log->date_create->timestamp) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ formatCustomDate($log->date_loaded?->timestamp) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $log->status }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('moyklass.info', $log) }}"
                                   class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Перейти</a>
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
