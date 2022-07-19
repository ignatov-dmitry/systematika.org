let addclass;
addclass = {

    openModal: function (email) {

        // Регистрируем событие отправки формы
        $('#formAddclass').submit(
            function (event) {
                addclass.add.sendForm();
                event.preventDefault();
            }
        );

        $('#addclassFormUserEmail').val(email);
        this.loadClasses.init();
        $('#modalAddclass').modal('show');


        //Notify.generate('test', 'head', '1');
    },


    loadClasses: {

        init: function () {
            this.load();
        },

        dataClasses: '',

        load: function () {
            if (!addclass.loadClasses.dataClasses)
                $.ajax({
                    'url': SETT.URL_SITE + '/mk-courses',
                    'type': 'GET',
                    success: function (response) {

                        addclass.loadClasses.dataClasses = response;
                        addclass.loadClasses.buildSelect();

                    }

                });

        },
        buildSelect: function (selectedId = 0) {

            let data = this.dataClasses;
            let selected;
            data = JSON.parse(data);
            $('#addclassFormSelectClasses').html('');
            data.forEach(course => {
                //console.log(course);


                $('#addclassFormSelectClasses').append(`<optgroup label="${course["name"]}">`);

                course["classes"].forEach(clas => {
                    // console.log(clas);
                    if (selectedId && selectedId === clas["id"])
                        selected = ' selected';
                    else
                        selected = '';

                    $('#addclassFormSelectClasses optgroup[label="' + course["name"] + '"]').append(`<option value="${clas["id"]}"${selected}>${clas["name"]}</option>`);
                });
                //


            });


        }
    },

    add: {

        sendForm: function () {
            $.ajax({
                'url': SETT.URL_SITE + '/addclass',
                'method': 'post',
                'data': $('#formAddclass').serialize(),
                'dataType': 'json',
                success: function (answer) {
                    $('#result_div').html(answer);


                    if (answer['status'] === 'error') {

                        if (answer['data'] === 'empty classId') {
                            Notify.generate('Не указана группа!', 'Ошибка!', 3);
                        } else if (answer['data'] === 'empty userEmail') {
                            Notify.generate('Не указан емейл пользователя!', 'Ошибка!', 3);
                        } else if (answer['data'] === 'user not found') {
                            Notify.generate('Пользователь с таким email не найден!', 'Ошибка!', 3);
                        } else {
                            Notify.generate('Произошла ошибка!', 'Ошибка!', 3);
                        }
                    }

                    console.log(answer['results']);
                    if (answer['results']) {
                        answer['results'].forEach(el => {

                            if (el['status'] === 'success') {
                                Notify.generate('Пользователь успешно добавлен в группу!', 'Успех!', 1);
                            }else{
                                let mess = '';
                                if(el['data']['message']==='Join already exists'){
                                    mess = 'Пользователь уже находится в указанной группе!';
                                }else{
                                    mess = el['data']['message'];
                                }
                                Notify.generate('Произошла ошибка!<br/>'+mess, 'Ошибка!', 3);
                            }


                        });

                    }
                }
                ,
                error: function (response) {
                    console.log(response);
                    $('#result_div').html('err:' + response);
                }

            });
        }

    }
};
