const classes = {


    init: function () {

        classes.loadList();

        $("#formClass").submit(
            function (event) {
                classes.sendForm(); // Делаем отправку формы
                event.preventDefault(); // Убираем стандартную отправку формы
            }
        );
    },
    sendForm: function () {
        if (!$('#classFormID').val()) {
            classes.add.sendForm();
        } else {
            classes.edit.sendForm();
        }
    },

    listData: '',
    loadList: function () {
        $.ajax({
            url: SETT.URL_SITE + '/settings/classes-ajax/',
            type: 'GET',
            dataType: 'html',
            success: function (response) {

                classes.listData = JSON.parse(response);

                var num = 0;
                var html = '';
                classes.listData.forEach(el => {

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
                <a href="#" onclick="classes.edit.openModal(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Редактировать" style="color: blue;">\
                    <i class="far fa-edit"></i></a>\
                <a href="#" onclick="classes.del(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Удалить" style="color: red;">\
                    <i class="far fa-trash-alt"></i></a>\
            </td>\
        </tr>';
                });
                $('#classes_table tbody').html(html);
                // console.log(num);

                // $('#result_form').html(response);
            }
        });
    },


    add: {
        openModal: function () {
            $('#classModalLabel').html('Добавление класса');
            $('#classFormID').val('');
            $('#classFormName').val('');
            $('#classFormShortname').val('');
            $('#classFormSort').val('');
            $('#classFormDefault').prop('checked', false);
            $('#classFormShow').prop('checked', false);
            $('#modalClass').modal();
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/classes-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formClass").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                    // $('#result_form').html('Данные получены.');
                    // $('#result_form').html(response);
                    classes.loadList();
                    $('#modalClass').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    $('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });


        },

    },

    edit: {

        openModal: function (id) {
            $('#classModalLabel').html('Редактирование класса ID ' + id);

            $('#modalClass').modal();
            classes.listData.forEach(el => {
                if (el["id"] == id) {
                    $('#classFormID').val(el["id"]);
                    $('#classFormName').val(el["name"]);
                    $('#classFormShortname').val(el["shortname"]);
                    $('#classFormSort').val(el["sort"]);
                    if(el["default"] && el["default"]=='Да')
                        $('#classFormDefault').prop('checked', true);
                    else
                        $('#classFormDefault').prop('checked', false);
                    if(el["show"] && el["show"]=='Да')
                        $('#classFormShow').prop('checked', true);
                    else
                        $('#classFormShow').prop('checked', false);

                }
            });
            console.log(classes.listData);
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/classes-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formClass").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                    // $('#result_form').html('Данные получены.');
                    // $('#result_form').html(response);
                    classes.loadList();
                    $('#modalClass').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    //$('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });

        },
    },

    del: function (id) {
        $.ajax({
            url: SETT.URL_SITE + '/settings/classes-ajax/'+id,
            type: "DELETE",
            success: function (response) { //Данные отправлены успешно
                classes.loadList();
            },
            error: function (response) { // Данные не отправлены

            }
        });
    }

};

classes.init();