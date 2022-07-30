var URL_GKTOMK = 'https://systematika.org/gktomk';
var mkTargetSelector = 'li[data-reactid=".0.1.1.0.1.3"]';
var mkEmailSelector = '.user-email';

var intervalWidget = [];

var UserInfoMK = {


    addWidget: function (namewidget) {

        switch (namewidget) {
            default:
            case "userinfo":
                return UserInfoMK.widgetFunctions.userinfo();
            case "visitstatstable":
                return UserInfoMK.widgetFunctions.visitstatstable.init();
            case "recordgroup":
                return UserInfoMK.widgetFunctions.recordgroup.init();
        }
    },

    widgetStartInterval: function (namewidget) {

        intervalWidget[namewidget] = setInterval(function () {
            if (UserInfoMK.waitWindow()) {
                if (!UserInfoMK.checkWidget(namewidget)) {
                    UserInfoMK.addWidget(namewidget);
                }
            } else if (!UserInfoMK.waitWindow() && UserInfoMK.checkWidget(namewidget)) {
                $('#' + namewidget).remove();
            }
        }, 500);
    },
    waitWindow: function () {
        if ($(mkTargetSelector).length) {
            return 1;
        } else {
            mkGoAjax = 0;
            return 0;
        }
    },
    checkWidget: function (selector_id) {
        if ($('#' + selector_id).length) {
            return 1;
        } else {
            return 0;
        }
    },

    widgetFunctions: {

        userinfo: function () {

            /*let id = '.block-items-menu';*/
            let id = '[data-reactid=".0.1.1.1.0"]';
            let id_content = '#userinfo_content';
            let prefix = '<div id="userinfo" class="info-panel-group info-panel-small-text   "><div class="body"><div class="group-item to-user" id="userinfo_content">';
            let suffix = '</div></div></div>';
            let html;
            let individualCount = 0, groupCount = 0, lastvisit = '-', totalVisitedCount = 0;

            if (!UserInfoMK.checkWidget('userinfo')) {
                $(id).prepend(prefix + 'Загрузка абонементов...' + suffix);
            }


            let $url = URL_GKTOMK + '/widget/userinfo/' + $(mkEmailSelector).html();
            $.get($url, function (data) {



                console.log(data);
                /*data = '{"subscription":{"individual":{"itemCount":23,"visitCount":92,"visitedCount":0},"group":{"itemCount":13,"visitCount":104,"visitedCount":2},"all":{"itemCount":36,"visitCount":196,"visitedCount":2}},"datevisit":{"date_last_test_lesson":"02.10.2020","date_last_lesson":"02.10.2020"}}';*/

                data = JSON.parse(data);

                if(data.status=='error'){
                    $(id_content).html(data.text);
                    return 0;
                }

                console.log(data);


                html = 'Количество абонементов: ';
                if (data.subscription.individual) individualCount = data.subscription.individual.visitCount - data.subscription.individual.visitedCount;
                if (data.subscription.group) groupCount = data.subscription.group.visitCount - data.subscription.group.visitedCount;
                if (data.subscription.all && data.subscription.group.visitedCount) totalVisitedCount = data.subscription.group.visitedCount;
                if (data.datevisit.date_last_lesson) lastvisit = data.datevisit.date_last_lesson;

                if(individualCount < 0) individualCount = 0;
                if(groupCount < 0) groupCount = 0;
                if(totalVisitedCount < 0) totalVisitedCount = 0;


                html = 'Количество абонементов: ' +
                    individualCount +
                    ' инд, ' + groupCount +
                    ' груп<br/>Последнее посещение: ' + lastvisit + ', <b>Посетил занятий:</b> ' + totalVisitedCount;


                $(id_content).html(html);

            });
            return 1;
        },

        visitstatstable: {

            init: function () {

                // Ожидаем первый виджет
                if (!UserInfoMK.checkWidget('userinfo'))
                    return 0;

                let id = '#userinfo';
                let prefix = '<div id="visitstatstable" class="info-panel-group info-panel-small-text   "><div class="body"><div class="group-item to-user" id="visitstatstable_content">';
                let suffix = '</div></div></div>';
                let html = '';

                if (!UserInfoMK.checkWidget('visitstatstable')) {
                    $(id).after(prefix + 'Загрузка статистики...' + suffix);
                }


                let $url = URL_GKTOMK + '/widget/userrecords/' + $(mkEmailSelector).html();
                $.get($url, function (data) {

                    data = JSON.parse(data);
                    console.log(data);

                    if(data.status=='error'){
                        $('#visitstatstable_content').html(data.text);
                        return 0;
                    }

                    UserInfoMK.widgetFunctions.visitstatstable.tableConstruct(data);

                });

                return 1;
            },

            tableConstruct: function(data, idContent='visitstatstable_content', notuser=0){
                //console.log(data);
                let $this = UserInfoMK.widgetFunctions.visitstatstable;
                let table_content = '';
                for (var key in data) {
                    console.log(key);

                    let course = '',
                        $class = '',
                        time = '',
                        beforelastweek1 = '',
                        beforelastweek2 = '',
                        lastweek1 = '',
                        lastweek2 = '',
                        thisweek1 = '',
                        thisweek2 = '',
                        nextweek = '',
                        nextweek2 = '';

                    if (data[key].course){
                        course = data[key].course;
                        course_split = course.split(' ');
                        course_min = course[0];
                        if (course_split[1]) {
                            course_min += ' ' + course_split[1][0];
                        }
                    }

                    if (data[key].class)
                        $class = data[key].class;
                    if (data[key].beginTime)
                        time = data[key].beginTime;

                    if (data[key].beforelastweek){
                        if(data[key].beforelastweek.records)
                            beforelastweek1 = data[key].beforelastweek.records;
                        else
                            beforelastweek1 = 0;

                        if(notuser==0)
                            beforelastweek2 = $this.visitedTpl(data[key].beforelastweek);
                    }


                    if (data[key].lastweek){
                        if(data[key].lastweek.records)
                            lastweek1 = data[key].lastweek.records;
                        else
                            lastweek1 = 0;

                        if(notuser==0)
                            lastweek2 = $this.visitedTpl(data[key].lastweek);
                    }


                    if (data[key].thisweek){
                        if(data[key].thisweek.records)
                            thisweek1 = data[key].thisweek.records;
                        else
                            thisweek1 = 0;

                        if(notuser==0)
                            thisweek2 = $this.visitedTpl(data[key].thisweek);
                    }

                    if (data[key].nextweek){
                        if(data[key].nextweek.records)
                            nextweek = data[key].nextweek.records;
                        else
                            nextweek = 0;

                        if(notuser==0)
                            nextweek2 = $this.visitedTpl(data[key].nextweek);
                    }


                    table_content += `<tr><td title="${course}, ${$class} - ${time}">${$class} - ${time}<br/>${course_min}</td><td>${beforelastweek1}${beforelastweek2}</td><td>${lastweek1}${lastweek2}</td><td>${thisweek1}${thisweek2}</td><td>${nextweek}${nextweek2}</td></tr>`;
                    //console.log(table_content);
                }

                html = '<table class="table completed-tasks" style="overflow-y: hidden;">\n' +
                    '        <tbody><tr><td>Наим</td><td>Позап</td><td>Прошл</td><td>Тек</td><td>Буд</td></tr>\n' +
                    table_content +
                    '    </tbody></table>';
                $('#'+idContent).html(html);
            },

            visitedTpl: function(week){
                let result = '';
                   /* if(week.visited && week.visited=='visited'){
                        result = '<span style="color: green;">был</span>';
                    }else if(week.visited && week.visited=='goodreason' || week.cancel && week.cancel == 1){
                        result = '<span style="color: blue;">бп.проп</span>';
                    }else if(week.visited && week.visited=='dont' && week.week!=='nextweek'|| week.cancel && week.cancel > 1){
                        result = '<span style="color: orange;">пл.проп</span>';
                    }else if(week.visited && week.visited=='notrecord' ){
                        result = '<span style="color: black;">не зап.</span>';
                    }*/

                    if(week.visited && week.visited=='notrecord' ){
                        result = ''; // <span style="color: grey;">не зап.</span>
                    }else if(week.visited && (week.visited=='recorded' || week.visited=='dontpay' && week.week=='nextweek')){
                        result = '<span style="color: black;">зап.</span>';
                    }else if(week.visited && week.visited=='visit'){
                        result = '<span style="color: green;">был</span>';
                    }else if(week.visited && week.visited=='goodreason' || week.cancel && week.cancel == 1){
                        result = '<span style="color: blue;">бп.проп</span>';
                    }else if(week.visited && week.visited=='dontpay' && week.week!=='nextweek'|| week.cancel && week.cancel > 1) {
                        result = '<span style="color: orange;">пл.проп</span>';
                    }
                    //console.log(week);
                return '<br/>'+result;
            }

        },

        recordgroup: {

            init: function () {
                // Ожидаем Второй виджет ltShowModalBlock("b-2e152" )
                if (!UserInfoMK.checkWidget('visitstatstable'))
                    return 0;

                let id = '#visitstatstable';
                let prefix = '<div id="recordgroup" class="info-panel-group info-panel-small-text   "><div class="body"><div class="group-item to-user" id="recordlesson_content">';
                let suffix = '</div></div></div>';


                if (!UserInfoMK.checkWidget('recordgroup')) {
                    $(id).after(prefix + '<a href="#" onclick="UserInfoMK.widgetFunctions.recordgroup.openModal($(mkEmailSelector).html());">Записать клиента в группу</a>' + suffix);
                                    }

                window.addEventListener('message', function (e) {
                    if(e.data && e.data.type && e.data.type == 'sendform'){
                        UserInfoMK.widgetFunctions.visitstatstable.init();
                    }
                }, false);
            },

            openModal: function (email) {
                this.modal = gcModalFactory.create({
                    show: !1,
                    width: 800
                });
                this.modal.setContent('<iframe src="https://systematika.org/gktomk/addgroup?password=04d3ef88674a1a39d7659c5df4252d97&email='+email+'&frame=1" width="100%" height="600px" frameborder="0" align="center" style="display: block;" id="iframe_mk_addgroup">Ваш браузер не поддерживает плавающие фреймы</iframe>');
                this.modal.show();
            }



        }


    }

};
UserInfoMK.widgetStartInterval('userinfo');
UserInfoMK.widgetStartInterval('visitstatstable');
UserInfoMK.widgetStartInterval('recordgroup');