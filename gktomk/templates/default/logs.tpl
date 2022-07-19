<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Логи отправок в MoyKlass</h6>
   /*{%*LOGS*}
    <div class="media text-muted pt-3">
        <svg class="bd-placeholder-img mr-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>Placeholder</title><rect width="100%" height="100%" fill="#6f42c1"></rect><text x="50%" y="50%" fill="#6f42c1" dy=".3em">32x32</text></svg>
        <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">{*LOGS:gk_first_name*} {*LOGS:gk_last_name*} ({*LOGS:gk_email*})</strong>
            Статус: {?*LOGS:status="new"*}ожидает {*LOGS:status="new"*?}{?!*LOGS:status="new"*}{*LOGS:status*}{*LOGS:status="new"*?!}
        </p>
    </div>
   {*LOGS*%}*/
<div id="result_div"></div>

    <table class="table table-sm">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Имя</th>
            <th scope="col"></th>
            <th scope="col">Email</th>
            <th scope="col">Номер</th>
            <th scope="col">Отправлен</th>
            <th scope="col">Комментарий к заказу</th>
            <th scope="col">Статус</th>
            <th scope="col">Результат</th>
            <th scope="col">Программа</th>
        </tr>
        </thead>
        <tbody>
        {%*LOGS*}
            <tr>
                <th scope="row">{*LOGS:id*}</th>
                <td><strong class="d-block text-gray-dark">{*LOGS:gk_first_name*} {*LOGS:gk_last_name*}</strong></td>
                <td> <a href="{*URL_GK*}/user/control/user/update/id/{*LOGS:gk_uid*}" target="_blank">GK</a> {?*LOGS:status="success"*}<a href="{*URL_SITE*}/redirectmk/{*LOGS:gk_email*}" target="_blank">MK</a>{*LOGS:status="success"*?}</td>
                <td><a href="#" onclick="addclass.openModal('{*LOGS:gk_email*}');">{*LOGS:gk_email*}</a></td>
                <td>{*LOGS:gk_phone*}</td>
                <td data-toggle="tooltip" data-placement="top" data-html="true" title="Добавлен: {* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}{?*LOGS:date_update*}<br/>Отправлен: {* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}{*LOGS:date_update*?}"> {?*LOGS:date_update*}{* @*Logs*->timeFormat( *LOGS:date_update* ) *}{*LOGS:date_update*?}{?!*LOGS:date_update*}-{*LOGS:date_update*?!}</td>
                /*<td>{* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}</td>*/
                <td>{*LOGS:gk_comment*}</td>
                <td>{?*LOGS:status="new"*}ожидает{*LOGS:status="new"*?}{?*LOGS:status="success"*}отправлен{*LOGS:status="success"*?}</td>
                <td>{*LOGS:status_result*}</td>
                <td><i>{*LOGS:subscription_program*}</i></td>
            </tr>
            {*LOGS*%}
            </tbody>
            </table>

</div>

<div class="modal fade" id="modalAddclass" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Добавление пользователя в Группу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="formSync" action="">
                <div class="modal-body">
                    <input type="hidden" class="form-control" id="syncFormID" name="sync[id]">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Программа</label>
                        <input type="text" class="form-control" id="syncFormPROGRAM" name="sync[program]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Предложение в ГК</label>
                        <input type="text" class="form-control" id="syncFormGKOFFER" name="sync[gk_offer]" required="required">
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Абонемент в МК</label>
                        <input type="text" class="form-control" id="syncFormMKSUB" name="sync[mk_sub]" required="required">
                    </div>


                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" id="syncBtn">
                </div>
            </form>
        </div>
    </div>
</div>

<script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/logs.addclass.js?r={*@rand(1000000, 99999999)*}"></script>