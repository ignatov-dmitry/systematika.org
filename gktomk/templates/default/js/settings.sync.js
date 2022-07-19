let sync = {

    init: function () {

        sync.list.load();

        $("#formSync").submit(
            function (event) {
                sync.sendForm(); // Делаем отправку формы
                event.preventDefault(); // Убираем стандартную отправку формы
            }
        );
    },

    sendForm: function () {
        if (!$('#syncFormID').val()) {
            sync.add.sendForm();
        } else {
            sync.edit.sendForm();
        }
    },

    list: {

        data: '',
        load: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/sync-list-ajax/',
                type: 'GET',
                dataType: 'html',
                success: function (response) {
                    sync.list.data = JSON.parse(response);
                    var num = 0;
                    var html = '';
                    sync.list.data.forEach(el => {
                      //  console.log(el['id']);
                        num++;
                        html += '<tr>\
            <td>' + num + '</td>\
            <td>' + el["program"] + '</td>\
            <td><a href="' + SETT.URL_GK + '/pl/sales/offer/update?id=' + el["gk_offer"] + '" target="_blank">' + el["gk_offer"] + '</a></td>\
            <td>' + el["mk_sub"] + '</td>\
            <td>\
                <a href="#" onclick="sync.edit.openModal(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Редактировать" style="color: blue;">\
                    <i class="far fa-edit"></i></a>\
                <a href="#" onclick="sync.del(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Удалить" style="color: red;">\
                    <i class="far fa-trash-alt"></i></a>\
            </td>\
        </tr>';
                    });
                    $('#sync_table tbody').html(html);
                   // console.log(num);

                   // $('#result_form').html(response);
                }
            });
        }
    },

    add: {
        openModal: function () {
            $('#syncModalLabel').html('Добавление синхронизации');
            $('#syncFormID').val('');
            $('#syncFormPROGRAM').val('');
            $('#syncFormGKOFFER').val('');
            $('#syncFormMKSUB').val('');
            $('#syncFormDEMO').prop('checked', false);
            $('#syncFormINDIVIDUAL').prop('checked', false);
            $('#modalSync').modal();
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/sync-add/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formSync").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно

                   // $('#result_form').html('Данные получены.');
                   // $('#result_form').html(response);
                    sync.list.load();
                    $('#modalSync').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    $('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });


        },

    },

    edit: {

        openModal: function (id) {
            $('#syncModalLabel').html('Редактирование синхронизации ID ' + id);

            $('#modalSync').modal();
            sync.list.data.forEach(el => {
                if (el["id"] == id) {
                    $('#syncFormID').val(el["id"]);
                    $('#syncFormPROGRAM').val(el["program"]);
                    $('#syncFormGKOFFER').val(el["gk_offer"]);
                    $('#syncFormMKSUB').val(el["mk_sub"]);
                    if(el["demo"])
                        $('#syncFormDEMO').prop('checked', true);
                    else
                        $('#syncFormDEMO').prop('checked', false);
                    if(el["individual"])
                        $('#syncFormINDIVIDUAL').prop('checked', true);
                    else
                        $('#syncFormINDIVIDUAL').prop('checked', false);

                }
            });
            console.log(sync.list.data);
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/sync-edit/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formSync").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                   // $('#result_form').html('Данные получены.');
                   // $('#result_form').html(response);
                    sync.list.load();
                    $('#modalSync').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    //$('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });

        },
    },
    del: function (id) {
        $.ajax({
           url: SETT.URL_SITE + '/settings/sync/'+id,
            type: "DELETE",
            success: function (response) { //Данные отправлены успешно
                sync.list.load();
            },
            error: function (response) { // Данные не отправлены

            }
        });
    }
};

sync.init();