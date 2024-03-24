'use strict';
let schedule = {

    init: function () {

        $('[data-cancel-id]').on('click', function () {
            schedule.lessonCancel.openModal(this);

        });
        $('[data-cancel-id-success]').on('click', function () {
            schedule.lessonCancel.cancelBtn(this);
        });

        schedule.history.init();

    },

    lessonCancel: {

        openModal: function (el) {
            var id = $(el).attr('data-cancel-id');
            var daynumber = $(el).attr('data-cancel-daynumber');
            var monthtxt = $(el).attr('data-cancel-monthtxt');
            var weekday = $(el).attr('data-cancel-weekday');
            var lessonDate = $(el).attr('data-cancel-lessonDate');
            var beginTime = $(el).attr('data-cancel-beginTime');
            var endTime = $(el).attr('data-cancel-endTime');
            var courseName = $(el).attr('data-cancel-courseName');
            var className = $(el).attr('data-cancel-className');
            var classId = $(el).attr('data-cancel-classId');

            var date = new Date(lessonDate);
            var time = date.getTime() / 1000;
            schedule.lessonCancel.countCancelLimit(time, classId);

            var jsprefix = '#js-lesson-';
            $(jsprefix + 'id').html(id);
            $(jsprefix + 'daynumber').html(daynumber);
            $(jsprefix + 'monthtxt').html(monthtxt);
            $(jsprefix + 'weekday').html(weekday);
            $(jsprefix + 'beginTime').html(beginTime);
            $(jsprefix + 'endTime').html(endTime);
            $(jsprefix + 'courseName').html(courseName);
            $(jsprefix + 'className').html(className);

            $('#lessons-button-cancel-modal-success').attr('data-cancel-id-success', id);

            $('#lessonCancelModal').modal();
        },

        cancelBtn: function (el) {
            var btn = $('[data-cancel-id-success]');
            var btn_text_loading = btn.attr('data-cancel-text-loading');
            var btn_text = btn.text();
            btn.html(btn_text_loading);

            var id = $(el).attr('data-cancel-id-success');

            $.ajax({
                url: SETT.URL_SITE + '/schedule/ajax-cancel-lesson/',
                type: 'post',
                data: {lessonId: id},
                success: function (response) {

                    response = JSON.parse(response);

                    var lesson_cancel_id = '[data-lesson-id="' + id + '"]';
                    $(lesson_cancel_id).html(
                        $(lesson_cancel_id).attr('data-lesson-cancel-text')
                    );
                    $('#'+id+response['type']).show();
                    console.log(response);
                    Notify.generate('Вы отменили занятие №' + id, 'Отмена занятия', 1);
                    $('#lessonCancelModal').modal('hide');
                },
                error: function (response) {
                    console.log(response);
                    Notify.generate('Ошибка при отмене занятия №' + id, 'Отмена занятия', 3);

                }
            }).done(function () {
                btn.text(btn_text);
            });
        },

        countCancelLimit: function (lesson_date, class_id) {
            $.ajax({
                url: SETT.URL_SITE + '/schedule/ajax-cancel-limit?lesson_date=' + lesson_date + '&class_id=' + class_id,
                type: 'get',
                success: function (response) {

                    let json = JSON.parse(response);

                    let month_txt = {1: 'январе', 2: 'феврале', 3: 'марте', 4: 'апреле', 5: 'мае',
                        6: 'июне', 7: 'июле', 8: 'августе', 9: 'сентябре', 10:'октябре',
                        11: 'ноябре', 12: 'декабре'}
                    $('.cancel_limit_month').text(month_txt[json.date_month]);

                    if (json.count_limit && json.count_limit > 0) {

                        let count_text = { 1: 'одна', 2: 'две', 3: 'три', 4: 'четыре', 5: 'пять', 6: 'шесть',
                            7: 'семь', 8: 'восемь', 9: 'девять', 10: 'десять', 11: 'одинадцать', 12: 'двенадцать',
                        13: 'тринадцать', 14: 'четырнадцать', 15: 'пятнадцать'};

                        let end_word = {1: 'а', 2: 'ы', 3: 'ы', 4: 'ы', 5: '', 6: '',
                            7: '', 8: '', 9: '', 10: '', 11: '', 12: '',
                            13: '', 14: '', 15: ''};

                        $('endword').text(end_word[json.count_limit]);
                        $('odna').text(count_text[json.count_limit]);
                        $('otmena').text(declOfNum(json.count_limit, ['отмена', 'отмены', 'отмен']));
                        $('Dostupna').text(declOfNum(json.count_limit, ['Доступна', 'Доступны', 'Доступны']));
                        $('besplatnaya').text(declOfNum(json.count_limit, ['бесплатная', 'бесплатные', 'бесплатных']));

                        $('#cancel_limit_yes').show();
                        $('#cancel_limit_no').hide();
                    } else {
                        $('#cancel_limit_yes').hide();
                        $('#cancel_limit_no').show();
                    }
                },
            });
        },
    },

    history: {
        init: function () {
            $('#modalView').on('hide.bs.modal', function () {
                /* schedule.history.view.player.obj.api('pause');*/
                $('#player').html('');
            });
            schedule.history.load();
        },

        load: function () {
            $.ajax({
                url: SETT.URL_SITE + '/schedule/ajax-load-history',
                type: 'get',
            });
        },

        view: {
            player: {id: '', obj: ''},
            openModal: function (id) {

                let tr = $('.card[data-id="' + id + '"]');
                let date = tr.data('date');
                let classname = tr.data('class-name');
                let meeting_topic = tr.data('meeting-topic');
                let unassigned = tr.data('unassigned');

                var re = '-';
                var str = date;
                var newstr = str.replaceAll(re, '/');

                if (unassigned){
                    newstr = 'unassigned_videos/' + newstr;
                    classname = meeting_topic;
                }


                let url = SETT.URL_SITE + '/zoom/video/?v=videorecord/' + newstr + '/' + classname;
                let url_prev = SETT.URL_SITE + "/templates/default/images/logo-new.png.webp";


                if (window.parent) {
                    return window.parent.postMessage({
                        'type': 'view',
                        'url': url,
                        'url_prev': url_prev
                    }, "*");
                }


                console.log(url);

                $('#player').html('<iframe src="' + url + '" width="500" height="280" border="0">\n' +
                    '    Ваш браузер не поддерживает плавающие фреймы!\n' +
                    ' </iframe>');
                if (!schedule.history.view.player.obj) {


                    /*schedule.history.view.player.id = id;
                    schedule.history.view.player.obj = new Playerjs({
                        id: "player",
                        file: url,
                        poster: url_prev
                    });*/

                } else if (schedule.history.view.player.id !== id) {
                    /*schedule.history.view.player.obj.api('pause');
                    schedule.history.view.player.obj.api("play", url);*/
                } else if (schedule.history.view.player.id === id) {
                    /*schedule.history.view.player.obj.api("play");*/
                }

                $('#modalView').modal();

            }
        }
    },

    addgroup: {

        openModal: function () {
            $('#modalEmptyDialog').addClass('modal-lg');
            $('#modalEmptyDialog').addClass('modal-dialog-centered');
            $('#emptyModalLabel').html('Запись в группу');
            $('#modalEmptyContent').html('<iframe height="600px" width="100%" style="border: 0;" src="' + SETT.URL_SITE + '/addgroup/user-add?email='+scheduleEmail+'"></iframe>');
            $('#modalEmpty').modal();
        }
    },

    addindividual: {

        openModal: function () {
            $('#modalEmptyDialog').removeClass('modal-lg');
            $('#modalEmptyDialog').addClass('modal-dialog-centered');
            $('#emptyModalLabel').html('Заявка на подбор преподавателя');

            //$('#modalEmptyContent').html('<iframe height="575px" width="100%" style="border: 0;" src="' + SETT.URL_SITE + '/addgroup/individual"></iframe>');

            $.ajax({
                url: SETT.URL_SITE + '/addgroup/individual/',
                type: 'get',
                success: function (html) {
                    $('#modalEmptyContent').html(html);
                    $('#modalEmpty').modal('show');

                    $("#formAddgroupIndividual").submit(
                        function (event) {
                            schedule.addindividual.sendForm(); // Делаем отправку формы

                            event.preventDefault(); // Убираем стандартную отправку формы
                        }
                    );
                }
            });
        },
        sendForm: function () {
            $.ajax({
                url: SETT.URL_SITE + '/addgroup/individual-ajax/',
                type: "POST", //метод отправки
                dataType: "html", //формат данных
                data: $("#formAddgroupIndividual").serialize(),  // Сеарилизуем объект
                success: function (response) { //Данные отправлены успешно

                    if(response==1){
                        $('#modalEmptyContent').html(`<div class="modal-body"><div class='alert alert-success' role='alert'><h4>Спасибо!</h4>Ваша заявка принята! Мы свяжемся с вами в течение 1-2 рабочих дней</div></div>`)
                    }else{
                        Notify.generate('Произошла ошибка, попробуйте позже.', 'Ошибка!', 1);
                    }



                }
            });
        }
    },

    activity: {

        openModal: function(member_id, lesson_id, class_id){
            this.getData(member_id, lesson_id, class_id);
        },

        getData: function (member_id, lesson_id, class_id) {

            let url = SETT.URL_SITE + '/schedule/ajax-activity';

            $.ajax({
                    url: url+'?member_id=' + member_id + '&lesson_id=' + lesson_id+'&class_id='+class_id,
                    type: 'get',
                    success: function (response) {
                        console.log(response);
                        let data = JSON.parse(response), html = '';

                        if(data['result'] == null || data['result'].length < 1)
                            return;
                        console.log(data);
                        data['result'].forEach(el => {
                            let date = new Date(el['date_create']*1000).toLocaleString('ru', {
                                hour: 'numeric',
                                minute: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });

                            switch(el['action_type']){
                                case 'cancellesson':
                                    html += '<b>Отменен</b>';
                                    break;
                                case 'addgroup':
                                    html += '<b>Записан</b>';
                                    break;
                            }

                            if(el['action_member_id'] == el['create_member_id'])
                                create_member_name = 'самостоятельно';
                            else{
                                create_member_name = 'администратором';
                            }

                            if(data['admin'])
                                create_member_name = el['create_member_first_name'] + ' ' +el['create_member_last_name'];

                            html += ' '+date+' '+create_member_name+'<br/>';

                        });


                        $('#modalEmptyDialog').removeClass('modal-lg');
                        $('#modalEmptyDialog').addClass('modal-dialog-centered');
                        $('#modalEmptyContent').html('');
                        $('#emptyModalLabel').html(html);
                        $('#modalEmpty').modal();
                    }
                }
            );

        }

    }
};
schedule.init();