<div class="my-3 p-3 bg-white rounded shadow-sm">
    <h6 class="pb-2 mb-2">
        Добавление пользователя в группу
    </h6>

    <hr/>
    <div id="addgroupContent">
        /*
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                   aria-controls="pills-home" aria-selected="true">ОМ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
                   aria-controls="pills-profile" aria-selected="false">ШМ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab"
                   aria-controls="pills-contact" aria-selected="false">Шах</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">1
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">2</div>
            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">3</div>
        </div>
        */

        <ul class="nav nav-pills mb-3" id="programs-links">
        </ul>

        <ul class="nav nav-pills mb-3" id="classes-links">
        </ul>

        <ul class="nav nav-pills mb-3" id="groups-links">
        </ul>

    </div>
</div>


<div class="modal fade" id="modalAddgroup" tabindex="-1" aria-labelledby="modalAddgroup" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
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
                        <label><b>Преподаватель:</b></label> <span id="modalTeacherContent"></span>
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