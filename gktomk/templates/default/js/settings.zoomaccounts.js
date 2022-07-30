let zoomaccounts = {

    init: function () {

        zoomaccounts.list.load();

        $("#formZoomaccounts").submit(
            function (event) {
                zoomaccounts.sendForm(); // Делаем отправку формы
                event.preventDefault(); // Убираем стандартную отправку формы
            }
        );
    },

    sendForm: function () {
        if (!$('#zoomaccountsFormID').val()) {
            zoomaccounts.add.sendForm();
        } else {
            zoomaccounts.edit.sendForm();
        }
    },

    list: {

        data: '',
        load: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/zoomaccounts-list-ajax/',
                type: 'GET',
                dataType: 'html',
                success: function (response) {

                    zoomaccounts.list.data = JSON.parse(response);
                    console.log(zoomaccounts.list.data);
                    var num = 0;
                    var html = '';
                    zoomaccounts.list.data.forEach(el => {
                        //  console.log(el['id']);
                        num++;
                        html += '<tr>\
            <td>' + num + '</td>\
            <td>' + el["login"] + '</td>\
            <td>' + el["comment"] + '</td>\
            <td>\
                <a href="#" onclick="zoomaccounts.edit.openModal(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Редактировать" style="color: blue;">\
                    <i class="far fa-edit"></i></a>\
                <a href="#" onclick="zoomaccounts.del(' + el["id"] + ')" data-toggle="tooltip" data-placement="top" title="Удалить" style="color: red;">\
                    <i class="far fa-trash-alt"></i></a>\
            </td>\
        </tr>';
                    });
                    $('#zoomaccounts_table tbody').html(html);
                    // console.log(num);

                    // $('#result_form').html(response);
                }
            });
        }
    },

    add: {
        openModal: function () {
            $('#zoomaccountsModalLabel').html('Добавление аккаунта');
            $('[name="zoomaccounts[id]').val('');
            $('[name="zoomaccounts[login]"]').val('');
            $('[name="zoomaccounts[api_key]').val('');
            $('[name="zoomaccounts[api_secret]').val('');
            $('[name="zoomaccounts[comment]').val('');
            $('#modalZoomaccounts').modal();
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/zoomaccounts/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formZoomaccounts").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                    // $('#result_form').html('Данные получены.');
                    // $('#result_form').html(response);
                    zoomaccounts.list.load();
                    $('#modalZoomaccounts').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    $('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });


        },

    },

    edit: {

        openModal: function (id) {
            $('#zoomaccountsModalLabel').html('Редактирование аккаунта ID ' + id);

            $('#modalZoomaccounts').modal();
            zoomaccounts.list.data.forEach(el => {
                if (el["id"] == id) {
                    $('[name="zoomaccounts[id]').val(el["id"]);
                    $('[name="zoomaccounts[login]"]').val(el["login"]);
                    $('[name="zoomaccounts[api_key]').val(el["api_key"]);
                    $('[name="zoomaccounts[api_secret]').val(el["api_secret"]);
                    $('[name="zoomaccounts[comment]').val(el["comment"]);
                }
            });
            console.log(zoomaccounts.list.data);
        },

        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/zoomaccounts/edit/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formZoomaccounts").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно
                    console.log(response);
                    zoomaccounts.list.load();
                    $('#modalZoomaccounts').modal('hide');
                },
                error: function (response) { // Данные не отправлены
                    //$('#result_form').html('Ошибка. Данные не отправлены.' + response);
                }
            });

        },
    },
    del: function (id) {
        $.ajax({
            url: SETT.URL_SITE + '/settings/zoomaccounts/'+id,
            type: "DELETE",
            success: function (response) { //Данные отправлены успешно
                console.log(response);
                zoomaccounts.list.load();
            },
            error: function (response) { // Данные не отправлены

            }
        });
    }
};

zoomaccounts.init();