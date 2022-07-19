<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom pb-2 mb-0">Расписание занятий</h6>
    <div class="monthly" id="mycalendar"></div>
/*
    Mikhail Ovchinnikov, [12.03.21 11:16]
    [In reply to Mikhail Ovchinnikov]
    По вёрстке и внешнему виду вот так
    Выводим только запланированные (те, что будут или идут сейчас)
    Перестаем выводить урок через 2 часа после его начала

    Колонки
    Дата | время |программа | группа | ссылка

    Mikhail Ovchinnikov, [12.03.21 11:18]
    12 марта, пт       17:15 - 18:15      Олимпиадная математика   3-1-5   https://us02web.zoom.us/j/839670....


    */



    <style>

        table {
            width: 100%;
            table-layout: fixed;

        }
        td {
            width: 100%;
            word-wrap: break-word;
        }

    </style>

    <table class="table table-sm table-bordered">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Время</th>
            <th>Программа</th>
            <th>Группа</th>
            <th>Ссылка</th>
        </tr>
        </thead>
        <tbody>
        {%*LESSONS*}

<tr>
<th>{*LESSONS:date*}</th>
<th>{*LESSONS:beginTime*} - {*LESSONS:endTime*}</th>
<th>{*LESSONS:COURSE.name*}</th>
<th>{*LESSONS:CLASS.name*}</th>
<td>{*LESSONS:topic*}</td>
</tr>
        {*LESSONS*%}
        </tbody>
        </table>


       /* <small class="d-block text-end mt-3">
            <a href="#">All updates</a>
        </small>*/













</div>