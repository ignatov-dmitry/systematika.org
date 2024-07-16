<nav class="navbar navbar-expand-lg navbar-dark rounded" style="background-color: #6f42c1;">

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample10" aria-controls="navbarsExample10" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample10">
        <a class="navbar-brand" href="{*URL_SITE*}/">GetCourse + MoyKlass</a>
        <br/>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{*URL_SITE*}/">Главная</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdownLogs" data-toggle="dropdown" aria-haspopup="false" aria-expanded="true">Логи</a>
                <div class="dropdown-menu" aria-labelledby="dropdownLogs">
                    <a class="dropdown-item" href="/integration/getcource/updates/list">Обновление полей getcource</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/">Отправка абонементов</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/homework">Домашние задания</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/cancellesson">Отмены занятий</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/videorecords">Видео-записи занятий</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/videorecords/not-matched-videos">Нераспознанные видео</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Настройки</a>
                <div class="dropdown-menu" aria-labelledby="dropdown10">
                    /*<a class="dropdown-item" href="{*URL_SITE*}/sett-conn">Подключение</a>*/
                    <a class="dropdown-item" href="{*URL_SITE*}/settings/sync">Синхронизация абонементов</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/settings/groups">Синхронизация программ и классов</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/settings/homework-links">Синхронизация домашних заданий</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/settings/whatsapp">WhatsApp</a>
                    <a class="dropdown-item" href="https://systematika.org/integration/user-notification/users">Уведомления</a>
                </div>
            </li>
        </ul>
    </div>
</nav>