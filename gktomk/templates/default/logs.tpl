<div class="my-3 p-3 bg-white rounded shadow-sm" xmlns:color="http://www.w3.org/1999/xhtml">
    <h6 class="border-bottom border-gray pb-2 mb-0">Логи отправок в MoyKlass</h6>
    /*{%*LOGS*}
    <div class="media text-muted pt-3">
        <svg class="bd-placeholder-img mr-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg"
             preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: 32x32"><title>
                Placeholder</title>
            <rect width="100%" height="100%" fill="#6f42c1"></rect>
            <text x="50%" y="50%" fill="#6f42c1" dy=".3em">32x32</text>
        </svg>
        <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">{*LOGS:gk_first_name*} {*LOGS:gk_last_name*} ({*LOGS:gk_email*}
                )</strong>
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
            <th scope="col">Отправлен</th>
            <th scope="col">Комментарий к заказу</th>
            <th scope="col">Статус</th>
            <th scope="col">Программа</th>
        </tr>
        </thead>
        <tbody> 
        {%*LOGS*}
            <tr>
                <td><strong>{*LOGS:id*}</strong>{?*LOGS:gk_order*}<br/><a href="{*URL_GK*}/sales/control/deal/update/id/{*LOGS:gk_order*}" target="_blank">{*LOGS:gk_order*}</a>{?}</td>
                <td><strong class="d-block text-gray-dark">{*LOGS:gk_first_name*} {*LOGS:gk_last_name*}</strong> <a href="#" onclick="addclass.openModal('{*LOGS:gk_email*}', '{*LOGS:id*}');">{*LOGS:gk_email*}</a> <br/> {*LOGS:gk_phone*}</td>
                <td><a href="{*URL_GK*}/user/control/user/update/id/{*LOGS:gk_uid*}"
                       target="_blank">GK</a> {?*LOGS:status="success" | LOGS:status="createsubscription" | LOGS:status="setmoney" | LOGS:status="error_setmoney" | LOGS:status="error_createsubscription"*}<a
                            href="{*URL_SITE*}/redirectmk/{*LOGS:gk_email*}" target="_blank">MK</a>{?}</td>
                <td data-toggle="tooltip" data-placement="top" data-html="true"
                    title="Добавлен: {* @date("Y-m-d, H:i:s", *LOGS:date_add*) *}{?*LOGS:date_update*}<br/>Отправлен: {* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}{*LOGS:date_update*?}"> {?*LOGS:date_update*}{* @*Logs*->timeFormat( *LOGS:date_update* ) *}{*LOGS:date_update*?}{?!*LOGS:date_update*}-{*LOGS:date_update*?!}</td>
                /*<td>{* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}</td>*/
                <td>{*LOGS:gk_comment*}</td>
                <td>
                    {?*LOGS:status="new"*}ожидает{?}
                    {%*LOGS:ownLog*}

                    {?*LOGS:ownLog:text="Пользователь создан!"*}<span style="color: violet;">Юзер создан</span><br/>{?}
                    {?*LOGS:ownLog:text="Денежные средства начислены!"*}<span style="color: green;">Зач. на счет</span><br/>{?}
                    {?*LOGS:ownLog:text="Абонемент создан!"*}<span style="color: blue;">Аб. создан</span><br/>{?}
                    {?*LOGS:ownLog:text="Средства за абонемент списаны!"*}<span style="color: orange;">Средства списаны</span><br/>{?}

                     {?*LOGS:ownLog:text!="Пользователь создан!" & LOGS:ownLog:text!="Денежные средства начислены!" & LOGS:ownLog:text!="Абонемент создан!" & LOGS:ownLog:text!="Средства за абонемент списаны!"*}<span style="color: red;">{*LOGS:ownLog:text*}</span> {?}



                {*LOGS:ownLog*%}{?*LOGS:status_result*}{*LOGS:status_result*}{*LOGS:status_result*?}
                {?*LOGS:balans<0*}<span style="color: red;">Долг: {*LOGS:balans*}</span><br/>{?}
                </td>
                <td><i>{*LOGS:subscription_program*}</i></td>
            </tr>
            {*LOGS*%}
            </tbody>
            </table>

</div>

<div class="modal fade" id="modalAddclass_bak" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Добавление пользователя в Группу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="formAddclass" action="">
                <div class="modal-body">
                    <div class="form-group">
                    <input type="hidden" name="addclass[userEmail]" id="addclassFormUserEmail" value="">
                        <label for="selectClasses">Выберите группу (класс)</label>
                        <select class="form-control" id="addclassFormSelectClasses" name="addclass[classId]"><option disable>Загрузка... </option></select>
                      </div>


                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" id="syncBtn">
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalAddclass" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncModalLabel">Добавление пользователя<span id="addclassUserEmail"><</span> в Группу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>


 <div class="modal-body" id="addclassContent">

</div>




        </div>
    </div>
</div>


<script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/logs.addclass.js?r={*@rand(1000000, 99999999)*}"></script>