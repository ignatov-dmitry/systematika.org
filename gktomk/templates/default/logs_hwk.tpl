/*
* Шаблон для логов домашних заданий
*/

<div class="my-3 p-3 bg-white rounded shadow-sm" xmlns:color="http://www.w3.org/1999/xhtml">
    <h6 class="border-bottom border-gray pb-2 mb-0">Логи домашних заданий</h6>
<div id="result_div"></div>

    <table class="table table-sm">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Юзер</th>
            <th scope="col"></th>
            <th scope="col">MK урок ID</th>
            <th scope="col">Группа</th>
            <th scope="col">Отправлен</th>
            <th scope="col">Статус</th>
        </tr>
        </thead>
        <tbody>
        {%*LOGS*}
            <tr>
                <td><strong>{*LOGS:id*}</strong></td>
                <td>MK uID: {*LOGS:mk_user_id*}{?*LOGS:email*}<br/>{*LOGS:email*}{*LOGS:email*?}</td>
                <td><a href="https://app.moyklass.com/user/{*LOGS:mk_user_id*}/joins" target="_blank">MK</a>{?*LOGS:gk_uid*}<br/><a href="{*URL_GK*}/user/control/user/update/id/{*LOGS:gk_uid*}" target="_blank">GK</a>{?}{?!*LOGS:gk_uid*}{?*LOGS:email*}<br/><a href="{*URL_GK*}/pl/user/user/index?uc%5Bprofile_image%5D=0&uc%5Bname%5D=&uc%5Bemail%5D={*LOGS:email*}&uc%5Btype%5D=&uc%5Bstatus%5D=null&uc%5Bphone%5D=" target="_blank">GK</a>{?}{?}</td>

                <td>{*LOGS:mk_lesson_id*}</td>
                <td>{?!*LOGS:group*}-{?}{?*LOGS:group*}{*LOGS:group*}{?}</td>
                <td data-toggle="tooltip" data-placement="top" data-html="true"
                    title="Добавлен: {* @date("Y-m-d, H:i:s", *LOGS:date_add*) *}{?*LOGS:date_update*}<br/>Отправлен: {* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}{*LOGS:date_update*?}"> {?*LOGS:date_update*}{* @*Logs*->timeFormat( *LOGS:date_update* ) *}{*LOGS:date_update*?}{?!*LOGS:date_update*}-{*LOGS:date_update*?!}</td>
                <td><i>{?*LOGS:status="new"*}<span style="color: yellow;">В ожидании</span>{?}{?*LOGS:status="success"*}<span style="color: green;">Обработан</span>{?}{?*LOGS:status!="new" && LOGS:status!="success"*}{*LOGS:status*}{?}</i></td>
            </tr>
            {*LOGS*%}
            </tbody>
            </table>

    </div>