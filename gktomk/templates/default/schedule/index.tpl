<div class="lessons">
    <h3>Расписание занятий</h6>
        <!--div class="monthly" id="mycalendar"></div-->

        <script language="JavaScript">
            function openNewWin(url) {
                myWin= open(url);
            }

            function copytext(el) {
                var $tmp = $("<textarea>");

                $("body").append($tmp);
                $tmp.val($(el).text()).select();
                document.execCommand("copy");
                $tmp.remove();
                $(el).next().append('<div class="copied">скопировано</div>');

                $(el).next().fadeOut( "slow" );
            }
        </script>

        <style>
            body {background-color: #ffffff !important;}
            .lessons{background:#ffffff;}

            .lessons-table {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
            }
            .lessons-table tr{
                border-bottom: solid 1px #cccccc;
            }

            .lessons-button{
                background:#fde005;
                padding: 4px 15px;
                border:0;
                color:#333333;
                border-radius:20px;
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
            .far{font-size: 20px; cursor:pointer;}
            .copied{background-color:#d2e8b9; padding:3px;border-radius:4px;}

        </style>

        <table class="lessons-table">
            <thead>
            <tr>
                <th>Дата и время</th>
                <th>Группа</th>
                <th>Ссылка</th>
            </tr>
            </thead>
            <tbody>
            {%*LESSONS*}

            <tr>
                <td>
                    {*LESSONS:daynumber*} {*LESSONS:monthtxt*}, {*LESSONS:weekday*}
                    <br>
                    <div class="lessons-time">{*LESSONS:beginTime*} - {*LESSONS:endTime*}</div></td>
                <td>{*LESSONS:COURSE.name*}
                    <div class="lessons-group">{*LESSONS:CLASS.name*}</div>

                </td>
                <td><input type="button" value="Перейти" class="lessons-button" onclick="openNewWin('{*LESSONS:url*}');">&nbsp; <i class="far fa-copy" onclick="copytext('#lesson-url{*LESSONS:index*}');"></i>
                    <div id="lesson-url{*LESSONS:index*}"style="display:none;">{*LESSONS:url*}</div><span></span>
                    <!-- {*LESSONS:topic*} -->

                </td>
            </tr>
            {*LESSONS*%}
            </tbody>
        </table>


        /* <small class="d-block text-end mt-3">
            <a href="#">All updates</a>
        </small>*/













</div>