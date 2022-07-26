<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="border-bottom border-gray pb-2 mb-0">Логи сохранения видео-записей занятий</h6>
    <div id="result_div"></div>

    <table class="table table-sm table-responsive-sm">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Занятие</th>
            <th scope="col">Meeting Zoom</th>
            <th scope="col">Загрузка</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        {%*LOGS*}
        <tr data-id="{*LOGS:id*}" data-date="{*LOGS:date*}" data-class-name="{*LOGS:class_name*}">
            <td><strong>{*LOGS:id*}</strong> <br/>(ID: {*LOGS:lesson_id_mk*})</td>
            <td>{*LOGS:date*}, {*LOGS:begin_time*} - {*LOGS:end_time*} <br/>{?*LOGS:course_name*}{*LOGS:course_name*},{?} {*LOGS:class_name*}</td>
            <td data-id-meeting_topic="{*LOGS:id*}">{*LOGS:meeting_topic*}</td>
            <td data-id-status="{*LOGS:id*}"><i>{?*LOGS:status="new"*}<span style="color: yellow;">В ожидании</span>{?}{?*LOGS:status="OK"*}<span style="color: green;">Обработан</span>, попыток: {*LOGS:try_num*}{?}{?*LOGS:status!="new" && LOGS:status!="OK"*}{*LOGS:status*}, попыток: {*LOGS:try_num*}{?}</i></td>
            <td><button class="btn" onclick="videorecords.redownload.openModal({*LOGS:id*});" title="Перезакачать"><i class="fas fa-redo"></i></button><button class="btn {?!*LOGS:status="OK"*}d-none{?}" data-id-btnview="{*LOGS:id*}" onclick="videorecords.view.openModal({*LOGS:id*});"><i class="far fa-eye"></i></button></td>
        </tr>
        {*LOGS*%}
        </tbody>
    </table>

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


<script>SETT.URL_GK = '{*URL_GK*}';</script>
        <script defer src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/playerjs.js" type="text/javascript"></script>

        <script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/videorecords.js?r={*@rand(1000000, 99999999)*}"></script>


        /*<link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" />
        <script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>*/