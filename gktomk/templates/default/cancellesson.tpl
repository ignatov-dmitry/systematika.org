/*
* Шаблон для логов отмен занятий
*/

    <div class="my-3 p-3 bg-white rounded shadow-sm">
        <h6 class="border-bottom border-gray pb-2 mb-0">Логи отмен занятий</h6>
        <div id="result_div"></div>
        <table class="table table-sm table-responsive-sm table-responsive-md table-responsive-lg">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Пользователь</th>
                <th scope="col"></th>
                <th scope="col">Занятие</th>
                <th scope="col">Отправлен</th>
                <th scope="col">Тип</th>
                <th scope="col" title="Статус обработки системой">Тех. статус</th>
                <th scope="col" title="Статус обработки администратором">Адм. статус</th>
                <th scope="col">Комментарий</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            {%*LOGS*}
            <tr data-cancel-tr-id="{*LOGS:id*}"
                data-cancel-tr-name="{*LOGS:member.first_name*} {*LOGS:member.last_name*}"
                data-cancel-tr-email="{*LOGS:member.email*}"
                data-cancel-tr-datetime="{*LOGS:lesson_date*}, {*LOGS:lesson_begin_time*} - {*LOGS:lesson_end_time*}"
                data-cancel-tr-courseclass="{*LOGS:course_name*}, {*LOGS:class_name*}"
                data-cancel-tr-type="{*LOGS:type*}"
                data-cancel-tr-status="{*LOGS:status*}"
                data-cancel-tr-status-adm="{*LOGS:status_adm*}"
                data-cancel-tr-comment="{*LOGS:comment*}"
            >
                <td scope="row"><strong>{*LOGS:id*}</strong></td>
                <td>{*LOGS:member.first_name*} {*LOGS:member.last_name*} ({*LOGS:member.email*})</td>
                <td>
                    <a href="https://app.moyklass.com/user/{*LOGS:member.mk_uid*}/joins" target="_blank">MK</a>
                    {?*LOGS:member.gk_uid*}
                    <br/><a href="{*URL_GK*}/user/control/user/update/id/{*LOGS:member.gk_uid*}" target="_blank">GK</a>
                    {?}
                    {?!*LOGS:member.gk_uid*}
                    {?*LOGS:member.email*}
                    <br/><a href="{*URL_GK*}/pl/user/user/index?uc%5Bprofile_image%5D=0&uc%5Bname%5D=&uc%5Bemail%5D={*LOGS:member.email*}&uc%5Btype%5D=&uc%5Bstatus%5D=null&uc%5Bphone%5D="
                            target="_blank">GK</a>
                    {?}
                    {?}
                </td>
                <td>
                    {*LOGS:lesson_date*}, {*LOGS:lesson_begin_time*} - {*LOGS:lesson_end_time*}
                    <br/>
                    {*LOGS:course_name*}, {*LOGS:class_name*}
                </td>

                <td data-toggle="tooltip" data-placement="top" data-html="true"
                    title="Добавлен: {* @date("Y-m-d, H:i:s", *LOGS:date_create*) *}{?*LOGS:date_update*}<br/>Отправлен: {* @date("Y-m-d, H:i:s", *LOGS:date_update*) *}{*LOGS:date_update*?}">
                 {?*LOGS:date_update*}
                    {* @*Tplfunctions*->timeFormat( *LOGS:date_update* ) *}
                   {*LOGS:date_update*?}
                   {?!*LOGS:date_update*}-{?}
                </td>

            <td data-cancel-td-type-id="{*LOGS:id*}">
            {?*LOGS:type=1*}Бесплатная{?}
            {?*LOGS:type=2*}Более 8 часов{?}
            {?*LOGS:type=3*}Менее 8 часов{?}
            </td>
            <td data-cancel-td-status-id="{*LOGS:id*}">
                <i>{?*LOGS:status="new"*}<span style="color: var(--blue);">Новый</span>{?}
                    {?*LOGS:status="done"*}<span style="color: var(--success);">Обработан</span>{?}
                    {?*LOGS:status="in_progress"*}<span style="color: var(--warning);">В обработке</span>{?}
                    {?*LOGS:status="cancel"*}<span style="color: var(--red);">Отклонен</span>{?}
                </i>
                </td>
                <td data-cancel-td-status-adm-id="{*LOGS:id*}">
                    <i>{?*LOGS:status_adm="new"*}<span style="color: var(--blue);">Новый</span>{?}
                        {?*LOGS:status_adm="done"*}<span style="color: var(--success);">Обработан</span>{?}
                        {?*LOGS:status_adm="in_progress"*}<span style="color: var(--warning);">В обработке</span>{?}
                        {?*LOGS:status_adm="cancel"*}<span style="color: var(--red);">Отклонен</span>{?}
                    </i>
                </td>
                <td data-cancel-td-comment-id="{*LOGS:id*}">{*LOGS:comment*}</td>
                <td><a href="#" data-cancel-edit-id="{*LOGS:id*}" data-toggle="tooltip" data-placement="top"
                       title="Редактировать" style="color: blue;"><i
                                class="far fa-edit"></i></a></td>
            </tr>
            {%}
            </tbody>
        </table>
        {*PAGINATION*}
        <div class="modal fade " id="cancellessonModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class=" sm-10 md-10 lg-8 xl-8 xxl-8 modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Редактирование отмены <span
                                    id="cancelModalLabelNumber"></span></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cancelTextUser"><strong>Пользователь</strong></label><br/>
                            <span id="cancelTextUser"></span>
                        </div>
                        <div class="form-group">
                            <label for="cancelTextLesson"><strong>Занятие</strong></label><br/>
                            <span id="cancelTextLesson"></span>
                        </div>
                        <div class="form-group">
                            <label for="cancelTextLesson"><strong>Тех. статус</strong></label><br/>
                            <span id="cancelTextStatus"></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Адм. статус</label>
                            <select class="form-control" id="cancelEditStatusAdm" name="cancel-status-adm">
                                <option value="new">Новый</option>
                                <option value="in_progress">В обработке</option>
                                <option value="done">Обработан</option>
                                <option value="cancel">Отклонен</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Тип отмены</label>
                            <select class="form-control" id="cancelEditType" name="cancel-type">
                                <option value="1">Бесплатная</option>
                                <option value="2">Платная: Более 8 часов</option>
                                <option value="3">Платная: Менее 8 часов</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Комментарий (видят администраторы)</label>
                            <textarea class="form-control" id="cancelEditComment" name="cancel-comment"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" id="cancelBtn" data-cancel-text-loading="Загрузка...">
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>


<script defer type="text/javascript"
        src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/cancellesson.js?r={*@rand(1000000, 99999999)*}"></script>
