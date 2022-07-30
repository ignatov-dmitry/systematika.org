
<div class="p-3 bg-white">
    <div id="addgroupContent">

        <p id="programs-links">
            Загрузка...
        </p>

        <p id="classes-links">
        </p>


        <p>
            <a class="btn dropdown-toggle" data-toggle="collapse" style="display: none;" href="#collapseManagers" role="button" aria-expanded="false" aria-controls="collapseManagers">
                Преподаватели
            </a>
        </p>
        <div class="collapse" id="collapseManagers">
            <ul class="nav nav-pills mb-3" id="managers-links">
            </ul>
        </div>



        <p id="groups-links">
        </p>

    </div>
</div>


<div class="modal fade" id="modalAddgroup" tabindex="-1" aria-labelledby="modalAddgroup" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="addgroupModalLabel">Запись в группу</h5>
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

                    /*<p id="lessonstatvisits"></p>*/


                    <div class="form-group">
                        <label><b>Когда хотите начать?</b></label>
                    <div class="form-check">


                        <div class="row">

                            <div class="col" id="modalFormRadioNearest">
                                <input type="hidden" name="idLessonNearest" value="">
                                <input class="form-check-input" type="radio" name="dateLesson" id="dateLessonNearest"
                                       value="nearest" required>
                                <label class="form-check-label" for="dateLessonNearest">
                                    С ближайшего
                                </label>
                            </div>
                            <div class="col" id="modalFormRadioNext">
                                <input type="hidden" name="idLessonNext" value="">
                                <input class="form-check-input" type="radio" name="dateLesson" id="dateLessonNext"
                                       value="next">
                                <label class="form-check-label" for="dateLessonNext">
                                    Со следующего
                                </label>
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><b>Записаться</b></label>
                        <div class="form-check">
                        <div class="row">
                            <div class="col">
                                <input class="form-check-input" type="radio" name="periodLesson" id="periodLessonAlways"
                                       value="always" required checked>
                                <label class="form-check-label" for="periodLessonAlways">
                                    На постоянно
                                </label>
                            </div>
                            <div class="col">
                                <input class="form-check-input" type="radio" name="periodLesson" id="periodLessonOnetime"
                                       value="onetime" >
                                <label class="form-check-label" for="periodLessonOnetime">
                                    На один раз
                                </label>
                            </div>

                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer" style="display:none;">
                    <input type="submit" class="btn btn-primary" id="addgroupBtn" value="Записаться">
                </div>
            </form>
        </div>
    </div>
</div>


<script defer type="text/javascript"
        src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/addgroup.user.js?r={*@rand(1000000, 99999999)*}"></script>
<script defer type="text/javascript"
        src="{*URL_SITE*}/templates/widget_userinfo.js?r={*@rand(1000000, 99999999)*}"></script>



<style>
    button.close{
        padding:0;
        background-color:transparent;
        border:0
    }
    a.close.disabled{pointer-events:none}
    button{border-radius:0}
    button:focus{outline:1px dotted;outline:none -webkit-focus-ring-color}
    button,input,optgroup,select,textarea{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}button,input{overflow:visible}button,select{text-transform:none}[role=button]{cursor:pointer}select{word-wrap:normal}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button}[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){cursor:pointer}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{padding:0;border-style:none}input[type=checkbox],input[type=radio]{box-sizing:border-box;padding:0}textarea{overflow:auto;resize:vertical}fieldset{min-width:0;padding:0;margin:0;border:0}legend{display:block;width:100%;max-width:100%;padding:0;margin-bottom:.5rem;font-size:1.5rem;line-height:inherit;color:inherit;white-space:normal}progress{vertical-align:baseline}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{outline-offset:-2px;-webkit-appearance:none}[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{font:inherit;-webkit-appearance:button}

    .modal-header .close {
        padding: 0.7rem 0.7rem;
        margin: -1rem -1rem -1rem auto;
    }

    .close {
        float: right;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: .5;
    }

    .close:hover {
        color: #000;
        text-decoration: none;
    }
    .close:not(:disabled):not(.disabled):focus, .close:not(:disabled):not(.disabled):hover {
        opacity: .75;
    }
</style>