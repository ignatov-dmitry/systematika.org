
<div class="p-3 bg-white">
    <h6 class="pb-2 mb-2">
        Добавление пользователя в группу
    </h6>

    <hr/>
    <div id="addgroupContent">
        <ul class="nav nav-pills mb-3" id="programs-links">
            Загрузка...
        </ul>

        <ul class="nav nav-pills mb-3" id="classes-links">
        </ul>


        <p>
            <a class="btn dropdown-toggle" data-toggle="collapse" style="display: none;" href="#collapseManagers" role="button" aria-expanded="false" aria-controls="collapseManagers">
                Преподаватели
            </a>
        </p>
        <div class="collapse" id="collapseManagers">
                <ul class="nav nav-pills mb-3" id="managers-links">
                </ul>
        </div>



        <ul class="nav nav-pills mb-3" id="groups-links">
        </ul>

    </div>
</div>


<div class="modal fade" id="modalAddgroup" tabindex="-1" aria-labelledby="modalAddgroup" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="addgroupModalLabel">Добавление в группу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAddgroup" action="" method="post">
                <div class="modal-body" id="modalAddgroupLoading">Загрузка...</div>
                <div class="modal-body" id="modalAddgroupContent" style="display: none;">
                    <input type="hidden" name="idgroup" value="">
                    <input type="hidden" name="email" value="">

                    <div class="form-group">
                        <label><b>Преподаватель:</b></label>
                        <span id="modalTeacherContent"></span><br/>
                        <span id="modalProgramnameContent"></span>
                    </div>

                    <p id="lessonstatvisits"></p>

                    <div class="form-check">
                        <div class="row">
                            <div class="col" id="modalFormRadioNearest">
                                <input type="hidden" name="idLessonNearest" value="">
                                <input class="form-check-input" type="radio" name="dateLesson" id="dateLessonNearest"
                                       value="nearest" required>
                                <label class="form-check-label" for="dateLessonNearest">
                                    Ближайщее
                                </label>
                            </div>
                            <div class="col" id="modalFormRadioNext">
                                <input type="hidden" name="idLessonNext" value="">
                                <input class="form-check-input" type="radio" name="dateLesson" id="dateLessonNext"
                                       value="next">
                                <label class="form-check-label" for="dateLessonNext">
                                    Следующее
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <div class="row">
                            <div class="col">
                                <input class="form-check-input" type="radio" name="periodLesson" id="periodLessonOnetime"
                                       value="onetime" required checked>
                                <label class="form-check-label" for="periodLessonOnetime">
                                    На один раз
                                </label>
                            </div>
                            <div class="col">
                                <input class="form-check-input" type="radio" name="periodLesson" id="periodLessonAlways"
                                       value="always">
                                <label class="form-check-label" for="periodLessonAlways">
                                    На постоянно
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="display:none;">
                    <input type="submit" class="btn btn-primary" id="syncBtn" value="Добавить">
                </div>
            </form>
        </div>
    </div>
</div>


<script defer type="text/javascript"
        src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/addgroup.js?r={*@rand(1000000, 99999999)*}"></script>
<script defer type="text/javascript"
        src="{*URL_SITE*}/templates/widget_userinfo.js?r={*@rand(1000000, 99999999)*}"></script>