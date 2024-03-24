<div class="main-block">
    <div class="main-head">
        <div>
            {?*MEMBER.first_name*}<h1>{*MEMBER.last_name*} {*MEMBER.first_name*},</h1>{?}
            <p><span>Баланс уроков:</span> {*COUNT_SUBSCRIPTIONS_REMIND.group*} групповых; {*COUNT_SUBSCRIPTIONS_REMIND.individual*} индивидуальных <br> <a href="https://online.systematika.org/study-payment" target="_blank">Пополнить</a> </p>
        </div>
        <div class="mh-item">
            <a href="#!" onclick="schedule.addgroup.openModal();" class="joingroup"><i class="fas fa-plus-circle"></i> <span class="chat-pre">Запись в группу</span></a>
            <a href="#!" onclick="schedule.addindividual.openModal();" class="joingroup"><i class="fas fa-plus-circle"></i> <span class="chat-pre">Запись на индивидуальное</span></a>
            /*<a href="#!">
                        <span class="rel-span">
                            <i class="fas fa-comment-alt"></i>
                            <span class="ab-span">2</span>
                        </span>&nbsp;
                <span class="chat-pre">Чат с преподавателем</span>
            </a>*/
        </div>
    </div>

    <div class="main-body">
        <div class="body-head">
            <a class="link-navigation" href="{*URL_SITE*}/schedule">Будущие занятия</a>
            <a class="link-navigation-active" href="{*URL_SITE*}/schedule/history">Прошедшие занятия</a>
        </div>
        <div class="body-content">


        <!--div class="monthly" id="mycalendar"></div-->

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

            $(el).next().fadeOut("slow");
        }
    </script>

    <style>
        .lessons{background:#ffffff;} .lessons-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .lessons-table tr {
            border-bottom: solid 1px #cccccc;
        }

        .lessons-button {
            background: #fde005;
            padding: 4px 15px;
            border: 0;
            color: #333333;
            border-radius: 20px;
            font-family: "SFProText", sans-serif;
            font-size: 14px;
            letter-spacing: -0.4px;
        }

        .lessons-button-cancel {
            background: #fd0011;
            padding: 4px 15px;
            border: 0;
            color: #ffffff;
            border-radius: 20px;
            font-family: "SFProText", sans-serif;
            font-size: 14px;
            letter-spacing: -0.4px;
        }

        .lessons-text-cancel {
            background: #fd0011;
            padding: 5px 15px;
            color: #ffffff;
            border-radius: 25px;
            font-family: "SFProText", sans-serif;
            font-size: 14px;
            letter-spacing: -0.4px;
        }

        .lessons-button-view {
            background: #001afd;
            padding: 4px 15px;
            border: 0;
            color: #ffffff;
            border-radius: 20px;
            font-family: "SFProText", sans-serif;
            font-size: 14px;
            letter-spacing: -0.4px;
        }

        td {
            width: 100%;
            word-wrap: break-word;
            vertical-align: top;
            font-size: 14px;
            padding: 10px 0px 10px 0px;
        }

        th {
            vertical-align: bottom;
            font-size: 18px;
        }

        .far{font-size: 20px; cursor:pointer;} .copied

        {background-color:#d2e8b9; padding:3px;border-radius:4px;}

    </style>
            {%*LESSONS*}
            <div class="card" data-id="{*LESSONS:id*}" data-date="{*LESSONS:date*}" data-unassigned="{?*LESSONS:unassigned*}true{?}{?!*LESSONS:unassigned*}false{?}" data-meeting-topic="{*LESSONS:meeting_topic*}" data-class-name="{*LESSONS:class_name*}">
                <div class="card-top">
                    <div class="first">
                        <div class="date-item">
                            <span style="cursor: pointer;" onclick="schedule.activity.openModal({*MEMBER.id*}, {*LESSONS:id*}, {*LESSONS:class_id_mk*});"><i class="fas fa-calendar-day"></i></span>

                            <p><span> {*LESSONS:daynumber*} {*LESSONS:monthtxt*}, {*LESSONS:weekday*}</span><br> {*LESSONS:begin_time*} - {*LESSONS:end_time*}</p>
                        </div>
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
                    {?*LESSONS:teacher_id_mk*}
                    <div class="card-top-left">
                        <h1>Преподаватель <span>{%*TEACHERS*}{?*TEACHERS:id=LESSONS:teacher_id_mk*}{*TEACHERS:name*}{?}{%}</span></h1>
                    </div>
                    {?}
                </div>
                <div class="card-bottom">
                    <div>
                        <h1>{?*LESSONS:program.name*}{*LESSONS:program.name*}{?}{?!*LESSONS:program.name*}{*LESSONS:course_name*}{?}</h1>
                        <p>{?*LESSONS:class.name*}{*LESSONS:class.name*}, {?}группа: {*LESSONS:class_name*}</p>
                        {?*LESSONS:color>0*}<p><span style="font-size: 18px;color: {?*LESSONS:color=1*}#28ff00{?}{?*LESSONS:color=2*}#001afd{?};">&#x25CF;</span> {*LESSONS:color*}-й трек</p>{?}
                    </div>
                    <div class="card-bottom-left">
                        {?*LESSONS:status="OK" | LESSONS:path!=false*}<a href="#!" onclick="schedule.history.view.openModal({*LESSONS:id*});" class="blue-btn"><i class="fas fa-play"></i> Видео</a>{?}
                        {?*LESSONS:homework_link*}<a href="{*LESSONS:homework_link*}" target="_blank"><i class="fas fa-external-link-alt"></i> Материалы</a>{?}
                        <div id="lesson-url{*LESSONS:index*}" style="display:none;">{*LESSONS:url*}</div>
                    </div>
                </div>
            </div>

            {%}
            /*<tr data-id="*}{*LESSONS:id*}{*" data-date="*}{*LESSONS:date*}{*" data-class-name="*}{*LESSONS:class_name*}{*">
                <td>
                    *}{*LESSONS:daynumber*}{* *}{*LESSONS:monthtxt*}{*, *}{*LESSONS:weekday*}{*{?*LESSONS:year*}{* - *}{*LESSONS:year*}{* г.{?}
                    <br>
                    <div class="lessons-time">*}{*LESSONS:begin_time*}{* - *}{*LESSONS:end_time*}{*</div>
                </td>
                <td>*}{*LESSONS:course_name*}{*
                    <div class="lessons-group">*}{*LESSONS:class_name*}{*</div>
                </td>
                <td>{?*LESSONS:cancel*}{*
                    <span class='lessons-text-cancel'>Занятие отменено</span><br/>
                    {?*LESSONS:cancel=1*}{*<span style="font-size: small; color: #5a5a5a">без списания с абонемента</span><br/>{?}
                    {?*LESSONS:cancel=2*}{*<span style="font-size: small; color: #5a5a5a">списано занятие (исчерпан лимит отмен за *}{*LESSONS:mnths_nominative*}{*)</span><br/>{?}
                    {?*LESSONS:cancel=3*}{*<span style="font-size: small; color: #5a5a5a">списано занятие (отмена менее чем за 8 часов)</span><br/>{?}
                {?}

                    {?*LESSONS:videorecord*}{*<button class="lessons-button-view" onclick="schedule.history.view.openModal(*}{*LESSONS:id*}{*);">Смотреть</button>{?}</td>
            </tr>*/



<div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="width: 500px; height: 264px;">

            <div id="player"></div>

        </div>
    </div>
</div>
<script defer src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/playerjs.js" type="text/javascript"></script>


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