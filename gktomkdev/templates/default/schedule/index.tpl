<div class="main-block">
    <div class="main-head">
        <div>
            {?*MEMBER.first_name*}<h1>{*MEMBER.last_name*} {*MEMBER.first_name*},</h1>{?}
            <p><span>Баланс уроков:</span> {*COUNT_SUBSCRIPTIONS_REMIND.group*} групповых; {*COUNT_SUBSCRIPTIONS_REMIND.individual*} индивидуальных <br> <a href="https://online.systematika.org/study-payment" target="_blank">Пополнить</a> </p>
        </div>
        <div class="mh-item">
            <a href="#!" onclick="schedule.addgroup.openModal();" class="joingroup"><i class="fas fa-plus-circle"></i> <span class="chat-pre">Запись в группу</span></a>
            <a href="#!" onclick="schedule.addindividual.openModal();" class="joingroup"><i class="fas fa-plus-circle"></i> <span class="chat-pre">Запись на индивидуальное</span></a>
            <a href="{*URL_GK*}/chat">
                        <span class="rel-span">
                            <i class="fas fa-comment-alt"></i>
                            /*<span class="ab-span">2</span>*/
                        </span>
                <span class="chat-pre">Чат с преподавателем (beta)</span>
            </a>
        </div>
    </div>

    <div class="main-body">
        <div class="body-head">
            <a class="link-navigation-active" href="{*URL_SITE*}/schedule">Будущие занятия</a>
            <a class="link-navigation" href="{*URL_SITE*}/schedule/history">Прошедшие занятия</a>
        </div>
        <div class="body-content">



            {%*LESSONS*}
            <div class="card">
                <div class="card-top">
                    <div class="first">
                        <div class="date-item">
                            <span style="cursor: pointer;" onclick="schedule.activity.openModal({*MEMBER.id*}, {*LESSONS:id*}, {*LESSONS:CLASS.id*});"> <i class="fas fa-calendar-day"></i></span>

                            <p><span> {*LESSONS:daynumber*} {*LESSONS:monthtxt*}, {*LESSONS:weekday*}</span><br> {*LESSONS:beginTime*} - {*LESSONS:endTime*}</p>
                        </div>
                        {?!*LESSONS:cancel*}
                        <main data-lesson-id="{*LESSONS:id*}" data-lesson-cancel-text="<div class='zanyatiya'><p><span>Занятие отменено</span></div>">
                            <a id="lessons-button-cancel" data-cancel-id="{*LESSONS:id*}"
                               data-cancel-daynumber="{*LESSONS:daynumber*}"
                               data-cancel-monthtxt="{*LESSONS:monthtxt*}"
                               data-cancel-weekday="{*LESSONS:weekday*}"
                               data-cancel-lessonDate="{*LESSONS:date*}"
                               data-cancel-beginTime="{*LESSONS:beginTime*}"
                               data-cancel-endTime="{*LESSONS:endTime*}"
                               data-cancel-courseName="{*LESSONS:COURSE.name*}"
                               data-cancel-className="{*LESSONS:CLASS.name*}"
                               data-cancel-classId="{*LESSONS:CLASS.id*}"

                                    class="lessons-button-cancel cancel-button"><i class="far fa-times-circle"></i> <span>Отменить</span></a>
                            /*<a href="#!" class="transfer-button"><i class="fas fa-random"></i></i> Перенести</a>*/
                        </main>
                        {?}
                        {?*LESSONS:cancel*}
                        <div class="zanyatiya">
                            <p><span>Занятие отменено</span><br>
                                {?*LESSONS:cancel=1*}без списания с абонемента{?}
                                {?*LESSONS:cancel=2*}списано занятие (исчерпан лимит отмен за {*LESSONS:mnths_nominative*}){?}
                                {?*LESSONS:cancel=3*}списано занятие (отмена менее чем за 8 часов){?}
                            </p>
                        </div>
                        {?}
                    </div>
                    {?*LESSONS:CLASS.managerIds.0*}
                    <div class="card-top-left">
                        <h1>Преподаватель <span>{%*TEACHERS*}{?*TEACHERS:id=LESSONS:CLASS.managerIds.0*}{*TEACHERS:name*}{?}{%}</span></h1>
                    </div>
                    {?}

                </div>
                <div class="card-bottom">
                    <div>
                        <h1>{*LESSONS:program.name*}</h1>
                        <p>{?*LESSONS:class.name*}{*LESSONS:class.name*}, {?}группа: {*LESSONS:CLASS.name*}</p>
                        {?*LESSONS:color>0*}<p><span style="font-size: 18px;color: {?*LESSONS:color=1*}#28ff00{?}{?*LESSONS:color=2*}#001afd{?};">&#x25CF;</span> {*LESSONS:color*}-й трек</p>{?}
                    </div>
                    <div class="card-bottom-left">
                        <a href="{*LESSONS:url*}" target="_blank"><i class="fas fa-running"></i> Перейти</a>
                        <a href="#!" onclick="copytext('#lesson-url{*LESSONS:index*}');"><i class="far fa-copy"></i> Ссылка</a>
                        {?*LESSONS:homework_link*}<a href="{*LESSONS:homework_link*}" target="_blank"><i class="fas fa-external-link-alt"></i> Материалы</a>{?}
                        <div id="lesson-url{*LESSONS:index*}" style="display:none;">{*LESSONS:url*}</div>
                    </div>
                </div>
            </div>
            {%}


          /*


            <tr>
                <td>
                    {*LESSONS:daynumber*} {*LESSONS:monthtxt*}, {*LESSONS:weekday*}
                    <br>
                    <div class="lessons-time">{*LESSONS:beginTime*} - {*LESSONS:endTime*}</div>
                </td>
                <td>{*LESSONS:COURSE.name*}
                    <div class="lessons-group">{*LESSONS:CLASS.name*}</div>

                </td>
                {?!*LESSONS:cancel*}
                <td data-lesson-id="{*LESSONS:id*}" data-lesson-text-cancel="<span class='lessons-text-cancel'>Занятие отменено</span>"><input type="button" value="Перейти" class="lessons-button"
                                                                                                                                               onclick="openNewWin('{*LESSONS:url*}');">&nbsp;
                    <i class="far fa-copy" onclick="copytext('#lesson-url{*LESSONS:index*}');"></i>
                    <div id="lesson-url{*LESSONS:index*}" style="display:none;">{*LESSONS:url*}</div>
                    <span></span>
                    <input type="button" class="lessons-button-cancel" id="lessons-button-cancel" data-cancel-id="{*LESSONS:id*}"
                           data-cancel-daynumber="{*LESSONS:daynumber*}"
                           data-cancel-monthtxt="{*LESSONS:monthtxt*}"
                           data-cancel-weekday="{*LESSONS:weekday*}"
                           data-cancel-lessonDate="{*LESSONS:date*}"
                           data-cancel-beginTime="{*LESSONS:beginTime*}"
                           data-cancel-endTime="{*LESSONS:endTime*}"
                           data-cancel-courseName="{*LESSONS:COURSE.name*}"
                           data-cancel-className="{*LESSONS:CLASS.name*}"
                           data-cancel-classId="{*LESSONS:CLASS.id*}"
                           value="Отменить">
                    <!-- {*LESSONS:topic*} onclick="schedule.lessonCancel.openModal(this);" -->
                </td>
                {?}
                {?*LESSONS:cancel*}
                <td>
                    <span class='lessons-text-cancel'>Занятие отменено</span>
                </td>
                {?}
            </tr>
         */


<div class="modal fade" id="lessonCancelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="lessonCancelModalLabel">Отмена занятия № <span id="js-lesson-id"></span>
                    <span></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <p id="text-info-lesson">
                    Вы отменяете занятие:
                    <span id="js-lesson-daynumber"></span> <span id="js-lesson-monthtxt"></span>,
                    <span id="js-lesson-weekday"></span>,
                    <span id="js-lesson-beginTime"></span> - <span id="js-lesson-endTime"></span>
                    <br/>
                    <span id="js-lesson-courseName"></span>, Группа <span id="js-lesson-className"></span>
                </p><br/>
                <p><b>Правила отмены занятия:</b></p>

                <ul>
                    <li>- если до начала занятия остается более 8 часов и лимит отмен у вас <b>не исчерпан</b> - отмена
                        занятия будет бесплатной; <i>Вы получите ДЗ(если предусмотрено программой) и запись занятия</i>
                    </li>
                    <br/>
                    <li>- если до начала занятия остается более 8 часов и лимит отмен у вас <b>исчерпан</b> - отмена
                        занятия будет платной; <i>Вы получите ДЗ(если предусмотрено программой) и запись занятия</i>
                    </li>
                    <br/>
                    <li>- если до начала занятия остается менее 8 - отмена занятия будет платной; <i>Вы получите ДЗ(если предусмотрено программой) и запись занятия</i></li>
                    <br/>
                    <li>Чтобы перенести занятие на такое же в другой группе, напишите нам в раздел <a href="https://online.systematika.org/pl/talks/conversation" target="_blank">сообщения</a> или в мессенджер - <a href="https://wa.me/79850760560" target="_blank">whatsapp</a>, <a href="https://online.systematika.org/tlgrm" target="_blank">telegram</a>. Перенос осуществляется без оплаты. <a href="https://systematika.org/raspisanie" target="_blank">Расписание всех групп на сайте</a></li>
                </ul>


                <h3 class="modal-title">Вы подтверждаете отмену занятия?</h3>
            </div>
            <div class="modal-footer">
                <div class="row" style="display: inline-block;">
                    <div class="col" style="float: left;">
                        <p style="display: none;" class="text-left" id="cancel_limit_yes"><Dostupna></Dostupna> <b class="cancel_limit_count"><odna></odna></b> <besplatnaya></besplatnaya><br/> <otmena></otmena> в <b class="cancel_limit_month">декабре</b><br/><a data-toggle="collapse" href="#collapse" role="button" aria-expanded="false" aria-controls="collapse" style="color: #778072;text-decoration: none; border-bottom: 1px dashed #778072;">Что такое лимит отмен?</a></p>
                        <p style="display: none;" class="text-left" id="cancel_limit_no">Платная отмена (лимит<br/> бесплатных отмен в <br/><b class="cancel_limit_month">декабре</b> исчерпан)<br/><a data-toggle="collapse" href="#collapse" role="button" aria-expanded="false" aria-controls="collapse" style="color: #778072;text-decoration: none; border-bottom: 1px dashed #778072;">Что такое лимит отмен?</a></p>
                    </div>

                    <div class="col" style="float: right;">
                        <button type="button" class="btn btn-success" id="lessons-button-cancel-modal-success"
                                data-cancel-id-success="" data-cancel-text-loading="Загрузка...">Да, отменить
                        </button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Не отменять</button>
                    </div>

            </div>

            <div class="collapse" id="collapse">
                <div class="card card-body">
                    В месяц можно отменить одно занятие, если вы занимаетесь раз в неделю;<br/>
                    Два занятия в месяц, если вы занимаетесь 2 раза в неделю;<br/>
                    Три занятия в месяц, если вы занимаетесь 3 раза в неделю. И так далее<br/>
                    <br/>
                    Рассчитывается отдельно для групповых и индивидуальных занятий на каждый календарный месяц.
                    <br/><br/>
                    Формула расчёта на каждый месяц:<br/>
                    ЛИМИТ ОТМЕН = КОЛИЧЕСТВО ЗАПИСЕЙ / 4<br/>
                    <br/>
                    Пробные уроки в расчёте не учитываются.<br/>
                    Если по формуле ЛИМИТ ОТМЕН меньше 1, выдаётся одна отмена<br/>
                </div>
            </div>



        </div>
    </div>
</div>

</div>




        <script language="JavaScript">
            function openNewWin(url) {
                myWin = open(url);
            }

            function copytext(el) {
                var $tmp = $("<textarea>");

                $("body").append($tmp);
                $tmp.val($(el).text()).select();
                document.execCommand("copy");
                $tmp.remove();
                $(el).next().append('<div class="copied">скопировано</div>');
                Notify.generate('', 'Скопировано!', 1);

                $(el).next().fadeOut("slow");
            }
        </script>

    <style>

        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            margin-left: 5px;
            margin-right: 0;

         }


        button.bg-primary:focus,button.bg-primary:hover{background-color:#0062cc!important}.bg-secondary{background-color:#6c757d!important}a.bg-secondary:focus,a.bg-secondary:hover,button.bg-secondary:focus,button.bg-secondary:hover{background-color:#545b62!important}.bg-success{background-color:#28a745!important}a.bg-success:focus,a.bg-success:hover,button.bg-success:focus,button.bg-success:hover{background-color:#1e7e34!important}.bg-info{background-color:#17a2b8!important}a.bg-info:focus,a.bg-info:hover,button.bg-info:focus,button.bg-info:hover{background-color:#117a8b!important}.bg-warning{background-color:#ffc107!important}a.bg-warning:focus,a.bg-warning:hover,button.bg-warning:focus,button.bg-warning:hover{background-color:#d39e00!important}.bg-danger{background-color:#dc3545!important}a.bg-danger:focus,a.bg-danger:hover,button.bg-danger:focus,button.bg-danger:hover{background-color:#bd2130!important}.bg-light{background-color:#f8f9fa!important}a.bg-light:focus,a.bg-light:hover,button.bg-light:focus,button.bg-light:hover{background-color:#dae0e5!important}.bg-dark{background-color:#343a40!important}a.bg-dark:focus,a.bg-dark:hover,button.bg-dark:focus,button.bg-dark:hover{background-color:#1d2124!important}
        .alert{position:relative;padding:.75rem 1.25rem;margin-bottom:1rem;border:1px solid transparent;border-radius:.25rem}.alert-heading{color:inherit}.alert-link{font-weight:700}.alert-dismissible{padding-right:4rem}.alert-dismissible .close{position:absolute;top:0;right:0;padding:.75rem 1.25rem;color:inherit}.alert-primary{color:#004085;background-color:#cce5ff;border-color:#b8daff}.alert-primary hr{border-top-color:#9fcdff}.alert-primary .alert-link{color:#002752}.alert-secondary{color:#383d41;background-color:#e2e3e5;border-color:#d6d8db}.alert-secondary hr{border-top-color:#c8cbcf}.alert-secondary .alert-link{color:#202326}.alert-success{color:#155724;background-color:#d4edda;border-color:#c3e6cb}.alert-success hr{border-top-color:#b1dfbb}.alert-success .alert-link{color:#0b2e13}.alert-info{color:#0c5460;background-color:#d1ecf1;border-color:#bee5eb}.alert-info hr{border-top-color:#abdde5}.alert-info .alert-link{color:#062c33}.alert-warning{color:#856404;background-color:#fff3cd;border-color:#ffeeba}.alert-warning hr{border-top-color:#ffe8a1}.alert-warning .alert-link{color:#533f03}.alert-danger{color:#721c24;background-color:#f8d7da;border-color:#f5c6cb}.alert-danger hr{border-top-color:#f1b0b7}.alert-danger .alert-link{color:#491217}.alert-light{color:#818182;background-color:#fefefe;border-color:#fdfdfe}.alert-light hr{border-top-color:#ececf6}.alert-light .alert-link{color:#686868}.alert-dark{color:#1b1e21;background-color:#d6d8d9;border-color:#c6c8ca}.alert-dark hr{border-top-color:#b9bbbe}.alert-dark .alert-link{color:#040505}
        .modal-open{overflow:hidden}.modal-open .modal{overflow-x:hidden;overflow-y:auto}.modal{position:fixed;top:0;left:0;z-index:1050;display:none;width:100%;height:100%;overflow:hidden;outline:0}.modal-dialog{position:relative;width:auto;margin:.5rem;pointer-events:none}.modal.fade .modal-dialog{transition:-webkit-transform .3s ease-out;transition:transform .3s ease-out;transition:transform .3s ease-out,-webkit-transform .3s ease-out;-webkit-transform:translate(0,-50px);transform:translate(0,-50px)}@media (prefers-reduced-motion:reduce){.modal.fade .modal-dialog{transition:none}}.modal.show .modal-dialog{-webkit-transform:none;transform:none}.modal.modal-static .modal-dialog{-webkit-transform:scale(1.02);transform:scale(1.02)}.modal-dialog-scrollable{display:-ms-flexbox;display:flex;max-height:calc(100% - 1rem)}.modal-dialog-scrollable .modal-content{max-height:calc(100vh - 1rem);overflow:hidden}.modal-dialog-scrollable .modal-footer,.modal-dialog-scrollable .modal-header{-ms-flex-negative:0;flex-shrink:0}.modal-dialog-scrollable .modal-body{overflow-y:auto}.modal-dialog-centered{display:-ms-flexbox;display:flex;-ms-flex-align:center;align-items:center;min-height:calc(100% - 1rem)}.modal-dialog-centered::before{display:block;height:calc(100vh - 1rem);height:-webkit-min-content;height:-moz-min-content;height:min-content;content:""}.modal-dialog-centered.modal-dialog-scrollable{-ms-flex-direction:column;flex-direction:column;-ms-flex-pack:center;justify-content:center;height:100%}.modal-dialog-centered.modal-dialog-scrollable .modal-content{max-height:none}.modal-dialog-centered.modal-dialog-scrollable::before{content:none}.modal-content{position:relative;display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;width:100%;pointer-events:auto;background-color:#fff;background-clip:padding-box;border:1px solid rgba(0,0,0,.2);border-radius:.3rem;outline:0}.modal-backdrop{position:fixed;top:0;left:0;z-index:1040;width:100vw;height:100vh;background-color:#000}.modal-backdrop.fade{opacity:0}.modal-backdrop.show{opacity:.5}.modal-header{display:-ms-flexbox;display:flex;-ms-flex-align:start;align-items:flex-start;-ms-flex-pack:justify;justify-content:space-between;padding:1rem 1rem;border-bottom:1px solid #dee2e6;border-top-left-radius:calc(.3rem - 1px);border-top-right-radius:calc(.3rem - 1px)}.modal-header .close{padding:1rem 1rem;margin:-1rem -1rem -1rem auto}.modal-title{margin-bottom:0;line-height:1.5}.modal-body{position:relative;-ms-flex:1 1 auto;flex:1 1 auto;padding:1rem}.modal-footer{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-ms-flex-align:center;align-items:center;-ms-flex-pack:end;justify-content:flex-end;padding:.75rem;border-top:1px solid #dee2e6;border-bottom-right-radius:calc(.3rem - 1px);border-bottom-left-radius:calc(.3rem - 1px)}.modal-footer>*{margin:.25rem}.modal-scrollbar-measure{position:absolute;top:-9999px;width:50px;height:50px;overflow:scroll}@media (min-width:576px){.modal-dialog{max-width:500px;margin:1.75rem auto}.modal-dialog-scrollable{max-height:calc(100% - 3.5rem)}.modal-dialog-scrollable .modal-content{max-height:calc(100vh - 3.5rem)}.modal-dialog-centered{min-height:calc(100% - 3.5rem)}.modal-dialog-centered::before{height:calc(100vh - 3.5rem);height:-webkit-min-content;height:-moz-min-content;height:min-content}.modal-sm{max-width:300px}}@media (min-width:992px){.modal-lg,.modal-xl{max-width:800px}}@media (min-width:1200px){.modal-xl{max-width:1140px}}
            .collapse:not(.show){display:none}.collapsing{position:relative;height:0;overflow:hidden;transition:height .35s ease}@media (prefers-reduced-motion:reduce){.collapsing{transition:none}}


        .joingroup {
            width: 173px;
            height: 35px;
            background: #93DD81;
            border-radius: 7px;
            font-family: Roboto;
            font-style: normal;
            font-weight: normal;
            font-size: 16px;
            line-height: 19px;
            padding: 6px;
        }
        .joingroup a {
            color: black;
        }

        .container .main-block .main-head div a .chat-pre {
            color: #000000;
            margin: 10px;
            text-decoration: none;
        }


    </style>
