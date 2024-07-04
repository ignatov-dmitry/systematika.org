<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Логи сохранения видео-записей занятий</h6>
    <div id="result_div"></div>
    <form method="GET">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">Начальная дата</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{*date_from*}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to">Конечная дата</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" value="{*date_to*}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="program">Программа</label>
                        <select class="form-control" name="program" id="program">
                            <option value="">--Выберите программу--</option>
                            {%*PROGRAMS*}
                            <option value="{*PROGRAMS:id*}">{*PROGRAMS:name*}</option>
                            {*PROGRAMS*%}
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="meeting_topic">Название видео</label>
                        <input class="form-control" type="text" name="meeting_topic" id="meeting_topic" value="{*meeting_topic*}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Найти</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <table class="table table-sm table-responsive-sm">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Занятие</th>
            <th scope="col">Meeting Zoom</th>
            <th scope="col">Загрузка</th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        {%*LOGS*}
        <tr data-file-path="{*LOGS:file_path*}" data-id="{*LOGS:id*}" data-date="{*LOGS:date*}" data-modify="{?*LOGS:modify | LOGS:file_path*}true{?}{?!*LOGS:modify | !LOGS:file_path*}false{?}" data-unassigned="{?*LOGS:unassigned*}true{?}{?!*LOGS:unassigned*}false{?}" data-meeting-topic="{*LOGS:meeting_topic*}" data-class-name="{*LOGS:class_name*}">
            <td><strong>{*LOGS:id*}</strong> <br/>(ID: {*LOGS:lesson_id_mk*})</td>
            <td>{*LOGS:date*}, {*LOGS:begin_time*} - {*LOGS:end_time*} <br/>{?*LOGS:course_name*}{*LOGS:course_name*},{?} {*LOGS:class_name*}</td>
            <td data-id-meeting_topic="{*LOGS:id*}">{*LOGS:meeting_topic*}</td>
            <td data-id-status="{*LOGS:id*}"><i>{?*LOGS:status="new"*}<span style="color: yellow;">В ожидании</span>{?}{?*LOGS:status="OK" | LOGS:path!=false *}<span style="color: green;">Обработан</span>, попыток: {*LOGS:try_num*}{?}{?*LOGS:status!="new" && LOGS:status!="OK" && LOGS:path=false *}{*LOGS:status*}, попыток: {*LOGS:try_num*}{?}</i></td>
            <td>
                <button class="btn" onclick="videorecords.redownload.openModal({*LOGS:id*});" title="Перезакачать"><i class="fas fa-redo"></i></button>
                <button class="btn {?!*LOGS:status="OK" | LOGS:path!=false | LOGS:file_path*}d-none{?}" data-id-btnview="{*LOGS:id*}" onclick="videorecords.view.openModal({*LOGS:id*});"><i class="far fa-eye"></i></button>
            </td>
            <td>
                {?!*LOGS:path!=false*}{?}
                <button class="btn" onclick="videorecords.openUnassignedFolder.openModal({*LOGS:id*});" title="Открыть папку с неопределенными видео"><i class="fas fa-folder"></i></button>

                {?*LOGS:status="OK" | LOGS:path!=false | LOGS:file_path*}
                <button class="btn" data-id-btnview="{*LOGS:id*}" title="Копировать ссылку" onclick="videorecords.view.copyLink({*LOGS:id*});"><i class="far fa-copy"></i></button>
                {?}
            </td>
            <td>
                {?*LOGS:status="OK" | LOGS:path!=false | LOGS:file_path*}
                <button class="btn" data-id-btnview="{*LOGS:id*}" title="Копировать html код для вставки" onclick="videorecords.view.copyHtmlModal({*LOGS:id*});">
                    <i class="fa fa-code"></i>
                    {?*LOGS:is_safe="1"*}
                        <img title="Защищено от удаления" src="https://lh7-us.googleusercontent.com/g3e7G82I6BFLXlQKlVy8aD0HzF4zoRlH5T_Ez3cWi_22PXN2qji2VB5LVMBxnBKberStAX6TIrWa6sUJYvO_zvc6MFffH7E0WKDbrpHSGiWQk9lAP4qttfLpWGIS-oHQXNZkCiOlw_jun1hfjrDZu5Q" width="23" height="20" style="margin-left:0px;margin-top:0px;">
                    {?}
                </button>
                {?}
            </td>
        </tr>
        {*LOGS*%}
        </tbody>
    </table>
{*PAGINATION*}
</div>


<div class="modal fade" id="modalRedownload" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="redownloadModalLabel">Перезакачать видео-запись</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleFormControlInput1">Сделать новую попытку загрузки видео-записи?</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="buttonRedownload" class="btn btn-primary">Да</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">  <!--style="width: 500px; height: 264px;"-->

            <div id="player"></div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalUnassignedFolder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">  <!--style="width: 500px; height: 264px;"-->
            <div class="modal-header">
                <h5 class="modal-title" id="redownloadModalLabel">Выбрать видео</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="folders"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="copyHtmlLink" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">  <!--style="width: 500px; height: 264px;"-->
            <div class="modal-header">
                <h5 class="modal-title">Копировать html код</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6"><button onclick="videorecords.view.copyHtmlSafe()" id="copySafe" class="btn btn-primary">Скопировать код и защитить от удаления</button></div>
                    <div class="col-md-6"><button onclick="videorecords.view.copyHtmlUnsafe()" id="copyUnsafe" class="btn btn-primary">Скопировать код без защиты</button></div>
                </div>
                <div class="row">
                    <textarea style="margin-top: 20px;" class="form-control" id="htmlCode"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<script>SETT.URL_GK = '{*URL_GK*}';</script>
        <script defer src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/playerjs.js" type="text/javascript"></script>

        <script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/videorecords.js?r={*@rand(1000000, 99999999)*}"></script>


        /*<link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" />
        <script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>*/
