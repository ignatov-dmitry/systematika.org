var $_GET = function (name) {
    var url_string = window.location.href;
    var url = new URL(url_string);
    var c = url.searchParams.get(name);
    return c;
};

var addgroup = {

    init: function () {
        addgroup.loadPrograms.init();
        addgroup.loadGroups.init();
    },
    data: [],

    loadPrograms: {

        init: function () {
            addgroup.loadPrograms.loadLinks();

        },

        selectProgram: function (el) {
            $('.programs-link').removeClass('active');
            $(el).addClass('active');
            addgroup.loadPrograms.selectGroupByIdProgram($(el).attr('data-id-program'));
        },
        selectGroupByIdProgram: function (id_program) {
            addgroup.loadGroups.selectedGroups(id_program);
        },


        loadLinks: function () {
            $.ajax({
                url: SETT.URL_SITE + '/addgroup/data-ajax/',
                type: 'GET',
                dataType: 'html',
                success: function (response) {

                    addgroup.data = JSON.parse(response);

                    let links = '';

                    addgroup.data['programs'].forEach(program => {

                        if(!program['show'])
                            return;

                        let active = '';
                        if (program['default']) {
                            active = 'active';
                            addgroup.loadPrograms.selectGroupByIdProgram(program['id']);
                        }

                        links += '<li class="nav-item"><a href="#" onclick="addgroup.loadPrograms.selectProgram(this);" data-id-program="' + program['id'] + '" class="nav-link ' + active + ' programs-link">' + program['name'] + '</a></li>';
                    });
                    var lebel = '<div class="form-group">\n' +
                        '            <label><b>Программа:</b></label> <ul class="nav nav-pills mb-3">\n' +links+ '</ul>'+
                        '        </div>';
                    $('#programs-links').html(lebel);
                }
            });
        }


    },

    loadClasses: {

        addLinks: function (idProgram, idClass) {
            if (!addgroup.data['classes_selected'])
                addgroup.data.classes_selected = [];

            if (!addgroup.data['classes_selected'][idProgram])
                addgroup.data['classes_selected'][idProgram] = [];

            if (!addgroup.data['classes_selected'][idProgram].includes(idClass))
                addgroup.data['classes_selected'][idProgram].push(idClass);

        },

        loadLinks: function (idProgram) {
            if (!addgroup.data['classes_selected'] || !addgroup.data['classes_selected'][idProgram]) {
                $('#classes-links').html('');
                return;
            }

            let links = [];
            let num = 0;
            addgroup.data['classes_selected'][idProgram].forEach(class_id => {

                if(!addgroup.data['classes'][class_id]['show'])
                    return;

                num += 1;
                let nameLink = addgroup.data['classes'][class_id]['name'];
                let active = '';
                if (addgroup.data['classes'][class_id]['default']) {
                    active = 'active';
                    addgroup.loadClasses.selectGroupByClassId(idProgram, class_id);
                }
                links[addgroup.data['classes'][class_id]['sort']] = '<li class="nav-item"><a href="#" onclick="addgroup.loadClasses.selectClass(this);" data-id-class="' + class_id + '" class="nav-link ' + active + ' classes-link">' + nameLink + '</a></li>';
            });

            // Выводим в сортировке
            let links_sort = '';
            links.forEach(link => {
                links_sort += link;
            });


            var lebel = '<div class="form-group">\n' +
                '            <label><b>Класс:</b></label> <ul class="nav nav-pills mb-3">\n' +links_sort+ '</ul>'+
                '        </div>';
            $('#classes-links').html(lebel);

            if(num===1)
                $('a.nav-link.classes-link').addClass('active');


        },

        selectClass: function (el) {

            $('.classes-link').removeClass('active');
            $(el).addClass('active');
            addgroup.loadClasses.selectGroupByClassId($('.programs-link.active').attr('data-id-program'), $(el).attr('data-id-class'));
        },

        selectGroupByClassId: function (id_program, id_class) {
            addgroup.loadGroups.selectedGroups(id_program, id_class);
        }


    },
    /*
    * Загрузка учителей
    * */
    loadManagers: {

        addLinks: function (idProgram, idClass, idManager) {
            if (!addgroup.data['managers_selected'])
                addgroup.data.managers_selected = [];

            if (!addgroup.data['managers_selected'][idProgram])
                addgroup.data['managers_selected'][idProgram] = [];

            if (!addgroup.data['managers_selected'][idProgram][idClass])
                addgroup.data['managers_selected'][idProgram][idClass] = [];

            if (!addgroup.data['managers_selected'][idProgram][idClass].includes(idManager))
                addgroup.data['managers_selected'][idProgram][idClass].push(idManager);

        },

        loadLinks: function (idProgram, idClass) {

            // Если idClass не указан, берем из верстки
            if(!idClass)
                idClass = $('.classes-link.active').attr('data-id-class');

            if (!addgroup.data['managers_selected'] || !addgroup.data['managers_selected'][idProgram] || !addgroup.data['managers_selected'][idProgram][idClass]) {
                $('#managers-links').html('');
                $('a[href="#collapseManagers"]').hide();
                console.log('display hide');
                return;
            }

            let links = '';
            let num = 0;
            addgroup.data['managers_selected'][idProgram][idClass].forEach(manager_id => {
                num += 1;
                let nameManager = '';
                addgroup.data['managers'].forEach(manager => {
                    if(manager_id == manager['id'])
                        nameManager = manager['name'];
                });

                links += '<li class="nav-item"><a href="#" onclick="addgroup.loadManagers.selectManager(this);" data-id-manager="' + manager_id + '" class="nav-link managers-link">' + nameManager + '</a></li>';
            });

            $('#managers-links').html(links);

            if(num===1)
                $('a.nav-link.managers-link').addClass('active');


            if(num > 0){
                $('a[href="#collapseManagers"]').show();

            }



        },

        selectManager: function (el) {

            $('.managers-link').removeClass('active');
            $(el).addClass('active');
            addgroup.loadGroups.selectedGroups($('.programs-link.active').attr('data-id-program'), $('.classes-link.active').attr('data-id-class'), $(el).attr('data-id-manager'));
        },


    },

    loadGroups: {

        init: function () {
            $("#formAddgroup").submit(
                function (event) {
                    addgroup.loadGroups.sendForm(); // Делаем отправку формы
                    event.preventDefault(); // Убираем стандартную отправку формы
                }
            );
        },

        loadLinks: function (idProgram, idClass = 0, idManager = 0) {

            let links = '', num = 0;
            let day_weeks = {1: '', 2: '', 3: '', 4: '', 5: '', 6: '', 0: ''}, daysTimes = [];
            addgroup.data['groups_data'].forEach(group => {

                if (group['program_id'] !== idProgram || !group['show_user'])
                    return;

                if (!idClass)
                    addgroup.loadClasses.addLinks(idProgram, group['class_id']);
                else if (group['class_id'] != idClass)
                    return;


                if(group['manager_ids']){
                    let manager_ids = JSON.parse(group['manager_ids']);
                    let res = 'return';
                    manager_ids.forEach(manager_id => {
                        if (!idManager) {
                            addgroup.loadManagers.addLinks(idProgram, group['class_id'], manager_id);
                        }else if(idManager){
                            if(manager_id == idManager) // Нашли совпадение препода в группе
                                res = '';
                        }

                    });
                    if(idManager > 0 && res === 'return')
                        return;
                }/*else
                    return;*/

                let beginDate = new Date(group['begin_date']);
                let dayWeek = beginDate.getDay();
                let beginDay = (beginDate.getUTCHours() + 3), beginMinute = ((beginDate.getUTCMinutes() < 10 ? '0' : '') + beginDate.getUTCMinutes());
                let beginTime = beginDay + ':' + beginMinute;

                let color = '';
                if(group['color']==1){
                    color = 'green';
                }else if(group['color']==2){
                    color = 'blue';
                }else {
                    color = 'white';
                }

                let comment = '';
                if(group['comment']){
                    comment = '<br/><span style="font-size: small; color: #5a5a5a">'+group['comment']+'</span>';
                }


                // Делаем сортировку по времени для каждого дня недели
                if(!daysTimes[beginDay+''+beginMinute])
                    daysTimes[beginDay+''+beginMinute] = [];

                /*  <span style="font-size: 16px;color: '+color+';">&#x25CF;</span> */
                daysTimes[beginDay+''+beginMinute].push(['<li class="nav-item"><a href="#" data-id-group="' + group['group_id_mk'] + '" onclick="addgroup.loadGroups.openModal(this);" class="">' + beginTime + '</a> '+comment+'</li>', dayWeek]);

                num += 1;
            });


            //Выводим сортировку по времени по дню недели
            daysTimes.forEach(time => {
                time.forEach(days => {
                    if(!day_weeks[days[1]])
                        day_weeks[days[1]] = '';
                    day_weeks[days[1]] += days[0];
                });

            });


            let table = '';
            if (num > 0) {
                table += '<table class="table table-sm">' +
                    '<tr class="row mx-0">' +
                    '<th class="col">пн</th>' +
                    '<th class="col">вт</th>' +
                    '<th class="col">ср</th>' +
                    '<th class="col">чт</th>' +
                    '<th class="col">пт</th>' +
                    '<th class="col">сб</th>' +
                    '<th class="col">вс</th>' +
                    '</tr>' +
                    '<tr class="row mx-0">';
                table += '<td class="col">' + day_weeks[1] + '</td>';
                table += '<td class="col">' + day_weeks[2] + '</td>';
                table += '<td class="col">' + day_weeks[3] + '</td>';
                table += '<td class="col">' + day_weeks[4] + '</td>';
                table += '<td class="col">' + day_weeks[5] + '</td>';
                table += '<td class="col">' + day_weeks[6] + '</td>';
                table += '<td class="col">' + day_weeks[0] + '</td>';
                table += '</tr></table>';



            var lebel = '<div class="form-group">\n' +
                '            <label><b>Выберите группу:</b></label> <ul class="nav nav-pills mb-3">\n' +table+ '</ul>'+
                '        </div>';

            $('#groups-links').html(lebel);
            }else{
                $('#groups-links').html('');
            }


            // Загружаем классы, если они еще не загружены
            if (!idClass)
                addgroup.loadClasses.loadLinks(idProgram);

            // Загружаем преподавателей, если они еще не загружены
            if (!idManager)
                addgroup.loadManagers.loadLinks(idProgram, idClass);


        },

        selectedGroups: function (idProgram, idClass = 0, idManager = 0) {
            console.log('idProgram' + idProgram + 'idclass:' + idClass + 'idManager:' + idManager);
            addgroup.loadGroups.loadLinks(idProgram, idClass, idManager);
        },

        selectGroups: function(){
            addgroup.loadGroups.selectedGroups(
                $('.programs-link.active').attr('data-id-program'),
                $('.classes-link.active').attr('data-id-class'),
                $('.managers-link.active').attr('data-id-manager')
            );
        },

        openModal: function (el) {
            let modal = $('#modalAddgroup');

            let idGroup = $(el).attr('data-id-group');

            modal.modal({
                backdrop: false
            });
            modal.modal('show');

            $('#modalAddgroupContent').hide();
            $('.modal-footer').hide();
            $('#modalFormRadioNearest').hide();
            $('#modalFormRadioNext').hide();
            $('input[name="idgroup"]').val('0');
            $('input[name="email"]').val(' ');
            $('input[name="idLessonNext"]').val('0');
            $('input[name="idLessonNearest"]').val('0');

            $('#modalAddgroupLoading').slideDown(400);


            $.ajax({
                url: SETT.URL_SITE + '/addgroup/data-group-user-ajax/' + idGroup,
                type: 'GET',
                dataType: 'html',
                success: function (response) {
                    console.log(response);
                    addgroup.loadGroups.modalContentConstruct(response, 'modalAddgroupContent')
                }
            });
        },

        modalContentConstruct: function (data, idContent) {
            console.log(data);
            data = JSON.parse(data);


            let teacher_I = 0, teacherText = '';
            data['teachers'].forEach(teacher => {
                teacher_I++;
                teacherText += teacher;
                if (teacher_I < data['teachers'].length)
                    teacherText += ', ';
            });




            let programText = data['group']['programname']+', '+data['group']['classname']+', '+data['group']['weekdaytime'];

            let color = '', trackText = '';
            if(data['groupsync']['color']==1){
                color = '#28ff00';
                trackText = '1-й трек';
            }else if(data['groupsync']['color']==2){
                color = '#001afd';
                trackText = '2-й трек';
            }
            if(color)
                programText += '<br/><span style="font-size: 18px;color: '+color+';">&#x25CF;</span> '+trackText;




            $('input[name="idgroup"]').val(data['group']['id']);
            $('input[name="email"]').val($_GET('email'));

            $('#modalTeacherContent').html(teacherText);
            $('#modalProgramnameContent').html(programText);

            if (data['lessons']['0']) {
                $('#modalFormRadioNearest').show();
                $('input[name="idLessonNearest"]').attr('value', data['lessons']['0']['id']);
                $('#dateLessonNearest').attr('checked', 1);
                lessonDate = new Date(data['lessons']['0']['date']);
                month = lessonDate.toLocaleString('default', {month: 'short'});
                day = lessonDate.getDate();
                $('[for="dateLessonNearest"]').html('С ближайшего ' + day + ' ' + month);
            }


            if (data['lessons']['1']) {
                $('#modalFormRadioNext').show();
                $('input[name="idLessonNext"]').attr('value', data['lessons']['1']['id']);
                if (!data['lessons']['0'])
                    $('#dateLessonNext').attr('checked', 1)
                lessonDate = new Date(data['lessons']['1']['date']);
                month = lessonDate.toLocaleString('default', {month: 'short'});
                day = lessonDate.getDate();
                $('[for="dateLessonNext"]').html('Со следующего ' + day + ' ' + month);
            }

            $('#modalAddgroupLoading').slideUp(500);
            $('#modalAddgroupContent').slideDown(500);
            $('.modal-footer').slideDown(800);

        },

        sendForm: function () {
            $('#addgroupBtn').val('Запись...')
            $.ajax({
                url: SETT.URL_SITE + '/addgroup/addgroup-user-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formAddgroup").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    $('#addgroupBtn').val('Записаться')
                    resp = JSON.parse(response);
                    console.log(resp);

                    if (resp.addgroup && resp.addgroup.message) {
                        if (resp.addgroup.message == "Join already exists")
                            Notify.generate('Запись в группу уже существует.', 'Внимание!', 0, 6000);
                        else
                            Notify.generate(resp.addgroup.message, 'Ошибка!', 2, 6000);
                    } else if (resp.addgroup && resp.addgroup.id)
                        Notify.generate('Запись в группу произведена.', 'Успех!', 1, 6000);

                    if (resp.addlesson && resp.addlesson.message) {
                        if (resp.addlesson.message == "Record already exists in this lesson")
                            Notify.generate('Запись на урок уже существует.', 'Внимание!', 0, 6000);
                        else
                            Notify.generate(resp.addlesson.message, 'Ошибка!', 2, 6000);
                    } else if (resp.addlesson && resp.addlesson.id)
                        Notify.generate('Запись на урок создана.', 'Успех!', 1, 6000);


                    $('#modalAddgroup').modal('hide');
                },
                error: function (response) { // Данные не отправлены

                }
            });
        }

    }


};

addgroup.init();