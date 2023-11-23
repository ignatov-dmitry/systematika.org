const programs = {


    init: function () {

        programs.loadList();

        $("#formProgram").submit(
            function (event) {
                programs.sendForm(); // Делаем отправку формы
                event.preventDefault(); // Убираем стандартную отправку формы
            }
        );
    },
    sendForm: function () {
        if (!$('#programFormID').val()) {
            programs.add.sendForm();
        } else {
            programs.edit.sendForm();
        }
    },

    listData: '',
    loadList: function () {
        $.ajax({
            url: SETT.URL_SITE + '/settings/programs-ajax/',
            type: 'GET',
            dataType: 'html',
            success: function (response) {

                programs.listData = JSON.parse(response);

                var num = 0;
                var html = '';
                programs.listData.forEach(el => {
                    num++;


                    if(el["default"]==1)
                        el["default"] = 'Да';
                    else
                        el["default"] = 'Нет';

                    if(el["show"]==1)
                        el["show"] = 'Да';
                    else
                        el["show"] = 'Нет';

                    html += '<tr>\
            <td>' + num + '</td>\
            <td>' + el["name"] + '</td>\
            <td>' + el["shortname"] + '</td>\
            <td>' + el["default"] + '</td>\
            <td>' + el["show"] + '</td>\
            <td>\
                <a href="#" onclick="programs.edit.openModal(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Редактировать" style="color: blue;">\
                    <i class="far fa-edit"></i></a>\
                <a href="#" onclick="programs.del(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Удалить" style="color: red;">\
                    <i class="far fa-trash-alt"></i></a>\
            </td>\
        </tr>';
                });
                $('#programs_table tbody').html(html);
                // console.log(num);

                // $('#result_form').html(response);
            }
        });
    },


    add: {
        openModal: function () {
            $('#programModalLabel').html('Добавление программы');
            $('#programFormID').val('');
            $('#programFormName').val('');
            $('#programFormShortname').val('');
            $('#programFormSort').val('');
            $('#programFormDefault').prop('checked', false);
            $('#programFormShow').prop('checked', false);
            $('#modalProgram').modal();
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/programs-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formProgram").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                    // $('#result_form').html('Данные получены.');
                    // $('#result_form').html(response);
                    programs.loadList();
                    $('#modalProgram').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    $('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });


        },

    },

    edit: {

        openModal: function (id) {
            $('#programModalLabel').html('Редактирование программы ID ' + id);

            $('#modalProgram').modal();
            programs.listData.forEach(el => {
                if (el["id"] == id) {
                    $('#programFormID').val(el["id"]);
                    $('#programFormName').val(el["name"]);
                    $('#programFormShortname').val(el["shortname"]);
                    $('#programFormSort').val(el["sort"]);
                    if(el["default"] && el["default"]=='Да')
                        $('#programFormDefault').prop('checked', true);
                    else
                        $('#programFormDefault').prop('checked', false);
                    if(el["show"] && el["show"]=='Да')
                        $('#programFormShow').prop('checked', true);
                    else
                        $('#programFormShow').prop('checked', false);

                }
            });
            console.log(programs.listData);
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/programs-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formProgram").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                    // $('#result_form').html('Данные получены.');
                    // $('#result_form').html(response);
                    programs.loadList();
                    $('#modalProgram').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    //$('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });

        },
    },

    del: function (id) {
        $.ajax({
            url: SETT.URL_SITE + '/settings/programs-ajax/'+id,
            type: "DELETE",
            success: function (response) { //Данные отправлены успешно
                programs.loadList();
            },
            error: function (response) { // Данные не отправлены

            }
        });
    }

};

programs.init();