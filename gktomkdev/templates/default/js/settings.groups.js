const groups = {


    init: function () {


        groups.loadList();


        $("#formGroup").submit(
            function (event) {
                groups.sendForm(); // Делаем отправку формы
                event.preventDefault(); // Убираем стандартную отправку формы
            }
        );

        // Проводим синхронизацию, удаляем устаревшие группы
        groups.deleteInactiv();
    },
    regEvents: function () {

        /*$('[data-id-program]').onclick = function () {
            groups.loadGroup.init($(this).attr('data-id-program'));
            alert('click');
        };*/

        $('.input-form').onchange = function () {
            groups.saveForm(this);
        };
    },
    sendForm: function () {
        if (!$('#groupsFormID').val()) {
            groups.add.sendForm();
        } else {
            groups.edit.sendForm();
        }
    },

    saveForm: function (id) {
        groups.edit.sendForm($(id).attr('data-id-group'));
    },

    listData: [],
    loadGroup: {

        init: function (id_program) {

            let id_groups_content = '#groupsContent_' + id_program;

            if ($.trim($(id_groups_content).html()).length)
                return 1;


            groups.listData['groups'].forEach(program => {
                //alert(group['id']);
                if (id_program == program['id']) {

                    let groupsContent = '<table class="table table-sm"><thead><tr><th>Группа</th><th>Программа</th><th>Класс</th><th>ZOOM</th><th>Коммент</th><th>Цвет</th><th title="Группа для индивидуальных занятий (для учета при отмене занятий)">Инд.</th><th>Отобр. адм</th><th>Отобр. польз</th><th></th></tr></thead><tbody>';
                    let groupsContentInActive = '';
                    let openedactive = '',
                        openedinactive = '',
                        archiveactive = '',
                        archiveinactive = '';
                    program['classes'].forEach($class => {
                        let prefix = '';
                        if ($class['status'] !== 'opened') {
                            prefix = ' <span style="color: gray">(архив)</span>';
                        }
                        let style = '';

                        //console.log($class);
                        let idGroup = $class['id'];
                        let nameGroup = $class['name'];
                        let beginDate = new Date($class['beginDate']);
                        let beginTime = (beginDate.getUTCHours() + 3) + ':' + ((beginDate.getUTCMinutes() < 10 ? '0' : '') + beginDate.getUTCMinutes());

                        let idGroupCont = '<input type="hidden" data-id-group="' + idGroup + '" name="id" value="' + idGroup + '">';
                        let beginDateCont = '<input type="hidden" data-id-group="' + idGroup + '" name="begin_date" value="' + $class['beginDate'] + '">';

                        let managerIds = [];
                        let managerIdsCont = '';
                        $class['managerIds'].forEach(el => {
                            managerIdsCont += '<input type="hidden" data-id-group="' + idGroup + '" name="manager_ids[]" value="' + el + '">';
                        });

                        //let programCont = '<select class="form-control form-control-sm input-form" data-id-group="' + idGroup + '" name="program"><option>ОМ</option><option>ОШ</option></select>';
                        //let classCont = '<select class="form-control form-control-sm input-form" data-id-group="' + idGroup + '" name="class"><option>1</option><option>2</option></select>';

                        let program_id = 0;
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['program_id'])
                            program_id = groups.listData['groups_data'][idGroup]['program_id'];
                        let programCont = groups.loadGroup.buildSelectPrograms(idGroup, program_id);

                        let class_id = 0;
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['class_id'])
                            class_id = groups.listData['groups_data'][idGroup]['class_id'];
                        let classCont = groups.loadGroup.buildSelectClasses(idGroup, class_id);

                        let zoomaccount_id = 0;
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['zoomaccount_id'])
                            zoomaccount_id = groups.listData['groups_data'][idGroup]['zoomaccount_id'];
                        let zoomaccountCont = groups.loadGroup.buildSelectZoomaccounts(idGroup, zoomaccount_id);

                        let comment = '';
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['comment'])
                            comment = groups.listData['groups_data'][idGroup]['comment'];
                        let commentCont = '<input class="form-control form-control-sm input-form" type="text" placeholder="Комментарий" data-id-group="' + idGroup + '" name="comment" value="' + comment + '" onchange="groups.saveForm(this);">';


                        let color = 0;
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['color'])
                            color = groups.listData['groups_data'][idGroup]['color'];
                        let colorCont = groups.loadGroup.buildSelectColors(idGroup, color);

                        let individual = '';
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['individual'])
                            individual = 'checked';
                        let individualCont = '<div class="form-check"><input type="checkbox" class="form-check-input input-form" data-id-group="' + idGroup + '" name="individual" ' + individual + ' onchange="groups.saveForm(this);"></div>';


                        let showAdm = '';
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['show_adm'])
                            showAdm = 'checked';
                        let showAdmCont = '<div class="form-check"><input type="checkbox" class="form-check-input input-form" data-id-group="' + idGroup + '" name="show_adm" ' + showAdm + ' onchange="groups.saveForm(this);"></div>';

                        let showUser = '';
                        if (groups.listData['groups_data'][idGroup] && groups.listData['groups_data'][idGroup]['show_user'])
                            showUser = 'checked';
                        let showUserCont = '<div class="form-check"><input type="checkbox" class="form-check-input input-form" data-id-group="' + idGroup + '" name="show_user" ' + showUser + ' onchange="groups.saveForm(this);"></div>';


                        /*if (!program_id || !class_id) {
                            groupsContent += '<tr class="active" data-id-group="' + idGroup + '"><td>' + idGroupCont + ' ' + beginDateCont + managerIdsCont +' ' + beginTime + ', ' + nameGroup + '</td><td>' + programCont + '</td><td>' + classCont + '</td><td>' + zoomaccountCont + '</td><td>' + commentCont + '</td><td>' + colorCont + '</td><td>' + individualCont + '</td><td>' + showAdmCont + '</td><td>' + showUserCont + '</td><td></td></tr>';
                        } else if (program_id && class_id) {
                            groupsContentInActive += '<tr data-id-group="' + idGroup + '"><td>' + idGroupCont + ' ' + beginDateCont + managerIdsCont +' ' + beginTime + ', ' + nameGroup + '</td><td>' + programCont + '</td><td>' + classCont + '</td><td>' + zoomaccountCont + '</td><td>' + commentCont + '</td><td>' + colorCont + '</td><td>' + individualCont + '</td><td>' + showAdmCont + '</td><td>' + showUserCont + '</td><td></td></tr>';
                        }*/

                        let active = '', inactive = '', archive = '', sort = '';

                        if (!program_id || !class_id) {
                            active += 'class="active"';
                        }
                        let str = '<tr '+active+' data-id-group="' + idGroup + '"><td>' + idGroupCont + ' ' + beginDateCont + managerIdsCont +' '+prefix+' ' + beginTime + ', ' + nameGroup + '</td><td>' + programCont + '</td><td>' + classCont + '</td><td>' + zoomaccountCont + '</td><td>' + commentCont + '</td><td>' + colorCont + '</td><td>' + individualCont + '</td><td>' + showAdmCont + '</td><td>' + showUserCont + '</td><td></td></tr>';


                        switch($class['status']){
                            case 'opened':
                                if(active)
                                    openedactive += str;
                                else
                                    openedinactive += str;
                                break;

                            case 'archive':
                                if(active)
                                    archiveactive += str;
                                else
                                    archiveinactive += str;
                                break;
                        }

                    });
                    groupsContent += openedactive + openedinactive + archiveactive + archiveinactive;


                    groupsContent +='</tbody></table>';
                    $(id_groups_content).html(groupsContent);
                    groups.regEvents();
                }
            });
        },

        buildSelectClasses: function (idGroup, selected_id) {
            let Options = '';

            if (!selected_id) {
                Options += '<option value="NULL">-</option>';
            }
            groups.listData['classes'].forEach($class => {
                let name = $class['name'];
                if ($class['shortname'])
                    name = $class['shortname'];
                let id = $class['id'];
                let selected = '';
                if (selected_id == id)
                    selected = 'selected';

                Options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
            });


            return '<select onchange="groups.saveForm(this);" class="form-control form-control-sm input-form" data-id-group="' + idGroup + '" name="class">'
                + Options + '</select>';
        },

        buildSelectPrograms: function (idGroup, selected_id) {
            let Options = '';

            if (!selected_id) {
                Options += '<option value="NULL">-</option>';
            }
            groups.listData['programs'].forEach(program => {
                let name = program['name'];
                if (program['shortname'])
                    name = program['shortname'];
                let id = program['id'];
                let selected = '';
                if (selected_id == id)
                    selected = 'selected';

                Options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
            });


            return '<select onchange="groups.saveForm(this);" class="form-control form-control-sm input-form" data-id-group="' + idGroup + '" name="program">'
                + Options + '</select>';
        },

        buildSelectZoomaccounts: function (idGroup, selected_id) {
            let Options = '';
            Options += '<option value="NULL">-</option>';

            groups.listData['zoomaccounts'].forEach(zoomaccount => {
                let name = zoomaccount['email'];
                let id = zoomaccount['id'];
                let selected = '';
                if (selected_id == id)
                    selected = 'selected';

                Options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
            });


            return '<select onchange="groups.saveForm(this);" class="form-control form-control-sm input-form" data-id-group="' + idGroup + '" name="zoomaccount">'
                + Options + '</select>';
        },

        buildSelectColors: function (idGroup, selected_id) {
            let Options = '';

            if (!selected_id) {
                Options += '<option value="NULL">-</option>';
            }
            groups.listData['colors'].forEach(color => {
                let name = color['name'];
                if (color['shortname'])
                    name = color['shortname'];
                let id = color['id'];
                let selected = '';
                if (selected_id == id)
                    selected = 'selected';

                Options += '<option value="' + id + '" ' + selected + '>' + name + '</option>';
            });


            return '<select onchange="groups.saveForm(this);" class="form-control form-control-sm input-form" data-id-group="' + idGroup + '" name="color">'
                + Options + '</select>';
        }


    },
    loadList: function () {

        $.ajax({
            url: SETT.URL_SITE + '/settings/groups-ajax/',
            type: 'GET',
            dataType: 'html',
            success: function (response) {

                groups.listData = JSON.parse(response);
                console.log(groups.listData);
                var num = 0;
                var html = '';
                groups.listData['groups'].forEach(el => {

                    let id = el['id'];
                    let name = el['name'];

                    html += '<div class="card" >\n' +
                        '            <div class="card-header" id="heading' + id + '" data-id-program="' + id + '" onclick="groups.loadGroup.init(' + id + ');">\n' +
                        '                <h2 class="mb-0">\n' +
                        '                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse' + id + '" aria-expanded="true" aria-controls="collapse' + id + '">\n' +
                        '                        ' + name + '\n' +
                        '                    </button>\n' +
                        '                </h2>\n' +
                        '            </div>\n' +
                        '\n' +
                        '            <div id="collapse' + id + '" class="collapse" aria-labelledby="heading' + id + '" data-parent="#accordionGroups">\n' +
                        '                <div class="card-body groups-content" id="groupsContent_' + id + '">\n' +
                        '                    \n' +
                        '                </div>\n' +
                        '            </div>\n' +
                        '        </div>';

                });
                $('#accordionGroups').html(html);
                groups.regEvents();
            }
        });

    },

    edit: {

        sendForm: function (idGroup) {


            let data = $("[data-id-group='" + idGroup + "']").serialize();
            console.log(data);
            $.ajax({
                url: SETT.URL_SITE + '/settings/groups-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: data,
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                },
                error: function (response) { // Данные не отправлены
                    //$('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });

            let program = $('[name="program"][data-id-group="' + idGroup + '"]');
            let $class = $('[name="class"][data-id-group="' + idGroup + '"]');
            let tr = $('tr[data-id-group="' + idGroup + '"]');

            if (program.val() > 0 && $class.val() > 0) {
                tr.removeClass('active');
            } else {
                tr.addClass('active');
            }

        },
    },

    del: function (id) {
        $.ajax({
            url: SETT.URL_SITE + '/settings/groups-ajax/' + id,
            type: "DELETE",
            success: function (response) { //Данные отправлены успешно
                groups.loadList();
            },
            error: function (response) { // Данные не отправлены

            }
        });
    },

    deleteInactiv: function () {
        $.ajax({
            url: SETT.URL_SITE + '/addgroup/delete-inactiv-groups',
            type: "get",
        });
    }

};

groups.init();