let cancellesson = {

    init: function () {

        $('[data-cancel-edit-id]').on('click', function () {
            cancellesson.edit.init(this);
        });

        $('#cancelBtn').on('click', function () {
            cancellesson.edit.save(this);
        });

    },

    edit: {

        init: function (el) {
            const id = $(el).attr('data-cancel-edit-id');
            this.openModal(id);
        },

        openModal: function (id) {

            var pref = 'data-cancel-tr-';

            obj = $('[' + pref + 'id="' + id + '"]');

            let data = {
                name: obj.attr(pref + 'name'),
                email: obj.attr(pref + 'email'),
                datetime: obj.attr(pref + 'datetime'),
                courseclass: obj.attr(pref + 'courseclass'),
                type: obj.attr(pref + 'type'),
                status: obj.attr(pref + 'status'),
                statusadm: obj.attr(pref + 'status-adm'),
                comment: obj.attr(pref + 'comment'),
            };

            $('[name="cancel-type"] option[value="'+data.type+'"]').attr('selected', true);
            $('[name="cancel-status-adm"] option[value="'+data.statusadm+'"]').attr('selected', true);
            $('[name="cancel-comment"]').val(data.comment);
            if(data.name.trim().length>0)
                data.name += '<br/>';
            $('#cancelTextStatus').html(cancellesson.edit.tplStatus(data.status));
            $('#cancelTextUser').html(data.name+data.email);
            $('#cancelTextLesson').html(data.datetime+'<br/>'+data.courseclass);
            $('#cancelModalLabelNumber').text('№'+id);
            $('#cancelBtn').attr('data-cancel-btn-id', id);


            $('#cancellessonModal').modal();

        },

        save: function () {

            var btn = $('#cancelBtn');
            var btn_text_loading = btn.attr('data-cancel-text-loading');
            var btn_text = btn.text();
            btn.html(btn_text_loading);

            var id = btn.attr('data-cancel-btn-id');
            var type = $('[name="cancel-type"]').val();
            var status = $('[name="cancel-status"]').val();
            var statusadm = $('[name="cancel-status-adm"]').val();
            var comment = $('[name="cancel-comment"]').val();

            $.ajax({
                url: SETT.URL_SITE + '/cancellesson/ajax-cancel-save/',
                type: 'post',
                data: {id: id, type: type, status_adm: statusadm, comment: comment},
                success: function (response) {
                    console.log(response);
                    var pref = 'data-cancel-tr-';
                    $('[data-cancel-tr-id="'+id+'"]')
                        .attr(pref + 'status-adm', statusadm)
                        .attr(pref + 'type', type)
                        .attr(pref + 'comment', comment);
                    $('[data-cancel-td-status-adm-id="'+id+'"]').html(cancellesson.edit.tplStatus(statusadm));
                    $('[data-cancel-td-type-id="'+id+'"]').html(cancellesson.edit.tplType(type));
                    $('[data-cancel-td-comment-id="'+id+'"]').text(comment);
                    Notify.generate('Занятие №' + id + ' сохранено', 'Успех!', 1);
                    $('#cancellessonModal').modal('hide');
                },
                error: function (response) {
                    console.log(response);
                    Notify.generate('Ошибка при изменении занятия №' + id, 'Ошибка!', 3);

                }
            }).done(function(){
                btn.text(btn_text);
            });
        },

        tplStatus: function (status) {

            switch(status){
                case 'new':
                    status = '<span style="color: var(--blue);">Новый</span>';
                    break;
                case 'done':
                    status = '<span style="color: var(--success)">Обработан</span>';
                    break;
                case 'in_progress':
                    status = '<span style="color: var(--warning)">В обработке</span>';
                    break;
                case 'cancel':
                    status = '<span style="color: var(--red)">Отклонен</span>';
                    break;
            }
            return '<i>'+status+'</i>';
        },
        tplType: function (type) {

            switch(type){
                case '1':
                    type = 'Бесплатная';
                    break;
                case '2':
                    type = 'Более 8 часов';
                    break;
                case '3':
                    type = 'Менее 8 часов';
                    break;
            }
            return '<i>'+type+'</i>';
        }

    }
};

cancellesson.init();