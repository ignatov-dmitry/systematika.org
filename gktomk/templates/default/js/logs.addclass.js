let addclass;
addclass = {

    email: '',

    openModal: function (email) {

        addclass.email = email;

        $('#addclassUserEmail').html(' ('+email+')');

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
                    'url': SETT.URL_SITE + '/mkcourse',
                    'type': 'GET',
                    success: function (response) {
                        /*console.log(response);*/
                        addclass.loadClasses.dataClasses = response;
                        addclass.loadClasses.build();

                    }

                });

        },
        build: function () {
            $('#addclassContent').html(this.dataClasses);
        }
    },

    add: {

        send: function (classId) {



            $.ajax({
                'url': SETT.URL_SITE + '/addclass',
                'method': 'post',
                'data': { 'addclass': {'userEmail': addclass.email, 'classId': classId}  },
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
