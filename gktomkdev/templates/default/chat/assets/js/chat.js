$(function () {
    $(".heading-compose").click(function () {
        $(".side-two").css({
            "left": "0"
        });
    });

    $(".newMessage-back").click(function () {
        $(".side-two").css({
            "left": "-100%"
        });
    });

});
var isChatIntervalPaused = 0;
var chat = {

    interval: setInterval(
        function() {
            if(!isChatIntervalPaused) {
                chat.load.dialogs();
            }
        }, 1000),

    init: function () {

        chat.load.dialogs();


        document.addEventListener('keydown', function(event) {
            if (event.code == 'Enter' && (event.ctrlKey || event.metaKey)) {
                chat.load.sendMessage();
            }else{
                $('#dialogFormMessage').focus();
            }
        });

        if(SETT.MEMBER_MANAGER){
            this.interface.teacher();
        }

        if(SETT.MEMBER_ADMIN){
            this.interface.admin();
        }
    },

    cookie: {
        get: function (cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        },

        set: function (cname, cvalue, exdays = 30) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }
    },

    interface: {

        user: function () {

        },

        teacher: function () {

        },

        admin: function () {

        }

    },

    load: {

        events: function () {

            $('[name="dialogForm"]').submit(function () {
                chat.load.sendMessage();
            });


            $('#message_file').change(function() {
                if(!$('#message_file').prop('disabled'))
                    chat.load.sendFile();
            });

            $(".row.sideBar-body").click(function () {
                chat.cookie.set('dialog_id_selected', $(this).data('dialog-id'));
                setTimeout(200);
                if($('.app-one').width() < 768){ //screen.width < 811
                    $('.side').hide();
                }
                //chat.load.messages(this);
            });
        },

        managers: '',
        dialogsData: {},
        dialogsUpdateTime: 0,
        dialogs: function () {

            if(SETT.MEMBER_ADMIN==1){

                chat.load.dialogsAdmin.init();
            }else{
                chat.load.dialogsUserOrTeacher();
            }
        },

        getLoadDialogsAjax: function(){
            console.log('getLoadDialogsAjax');
            // Если диалоги уже загружены, обновляем только открытые
            if(this.dialogsUpdateTime){
                this.getLoadDialogsOpenAjax();
            }else{
                $.ajax({
                    url: SETT.URL_SITE + '/chat/dialogs-ajax/',
                    type: 'GET',
                    async: true,
                    dataType: 'html',
                    success: function (data) {
                        chat.cookie.set('new_select_teacher', 0);
                        chat.load.dialogsUpdateTime = (Date.now()/1000);
                        chat.load.getLoadDialogsAjaxHandler(data);
                        if(SETT.MEMBER_ADMIN){
                            $('#buttonTeachers').html('<i class="fa fa-comments fa-2x  pull-right" aria-hidden="true"></i>');
                        }
                    }
                });
            }
        },
        // Загрузка и обновление только открытых чатов
        getLoadDialogsOpenAjax: function(){
            console.log('getLoadDialogsOpenAjax');
            $.ajax({
                url: SETT.URL_SITE + '/chat/dialogs-open-ajax/?date_update='+this.dialogsUpdateTime,
                type: 'GET',
                async: true,
                dataType: 'html',
                success: function (data) {
                    console.log('getLoadDialogsOpenAjax');
                    console.log(data);
                    //

                    chat.load.getLoadDialogsOpenAjaxHandler(data);
                }
            });
        },

        getLoadDialogsOpenAjaxHandler: function(data){

            console.log(chat.load.dialogsUpdateTime);
            let response = JSON.parse(data);
            //console.log(response['dialogs']);
            let dialogs = response['dialogs'];

            let contentCalladmin = '', contentUpdates = '', contentUpdatesArray = [];

            for (const [key, valuedialog] of Object.entries(dialogs)) {
                let id = '[data-dialog-id="'+valuedialog['dialog_id']+'"]';
                if($(id).length>0){


                    $(id+' span.time-meta.pull-right').text(chat.load.timeTpl(valuedialog['lastmessage_time']*1000));
                    if(SETT.MEMBER_MANAGER || SETT.MEMBER_ADMIN)
                        if(valuedialog['count_unread_messages_manager'] > 0)
                            $(id+' span.unread-meta.pull-right span').text(valuedialog['count_unread_messages_manager']).show();
                        else
                            $(id+' span.unread-meta.pull-right span').text('').hide();
                    else
                        if(valuedialog['count_unread_messages_client'] > 0)
                            $(id+' span.unread-meta.pull-right span').text(valuedialog['count_unread_messages_client']).show();
                        else
                            $(id+' span.unread-meta.pull-right span').text('').hide();

                    if(chat.load.dialogsUpdateTime < valuedialog['date_update'])
                        chat.load.dialogsUpdateTime = valuedialog['date_update'];

                    if(valuedialog['banned'] && valuedialog['banned']==1){
                        if(SETT.MEMBER_ADMIN || SETT.MEMBER_ADMIN == 1){
                            $('[data-dialog-id="'+valuedialog['dialog_id']+'"]').remove();
                            continue;
                        }
                    }

                    if(valuedialog['calladmin']==1){
                        $(id).attr('data-dialog-calladmin', '1');
                    }else{
                        $(id).attr('data-dialog-calladmin', '0');
                        $(id+' span.name-meta').css('font-weight', 'normal');
                    }
                    if(SETT.MEMBER_ADMIN && valuedialog['calladmin']==1){
                        $(id+' span.name-meta').css('font-weight', 'bold');
                        contentCalladmin += $("<div />").append($(id).clone()).html();
                        $(id).remove();
                        continue;
                    }

                    //$('[data-dialog-id="'+valuedialog['dialog_id']+'"]').remove();
                    if(!contentUpdatesArray[valuedialog['date_update']])
                        contentUpdatesArray[valuedialog['date_update']] = '';

                    contentUpdatesArray[valuedialog['date_update']] += $("<div />").append($(id).clone()).html();
                    $(id).remove();

                }else{ // Добавился новый диалог
                    if(!contentUpdatesArray[valuedialog['date_update']])
                        contentUpdatesArray[valuedialog['date_update']] = '';
                    contentUpdatesArray[valuedialog['date_update']] += this.buildHTMLdialog(valuedialog);
                }
            }


            //console.log(contentUpdatesArray);
            if(contentUpdatesArray){
                //console.log('перебор');
                //contentUpdatesArray = contentUpdatesArray.sort(); ///Добавить сортировку
                for (const [key, value] of Object.entries(contentUpdatesArray)) {
                    contentUpdates += value;
                };

            }

            ///console.log(contentUpdates);


            $('#chatDialogs').prepend(contentCalladmin+contentUpdates); //
            chat.load.events();
            let dialog_id_selected = chat.cookie.get('dialog_id_selected');
            if($('[data-dialog-id="'+dialog_id_selected+'"]').length>0 && dialog_id_selected){
                chat.load.messages('[data-dialog-id="'+dialog_id_selected+'"]');
            }
                //$('[data-dialog-id="'+dialog_id_selected+'"]').click();

            // Продолжаем выполнение интервала
            isChatIntervalPaused = 0;
        },

        getLoadDialogsAjaxHandler: function(data){
            let response = JSON.parse(data);
            //console.log(response['dialogs']);
            let dialogs = response['dialogs'];
            this.managers = response['managers'];

            let contentDialogs = '', access = 0;
            for (const [key, valuedialog] of Object.entries(dialogs)) {
                contentDialogs += this.buildHTMLdialog(valuedialog);

            }

            $('#chatDialogs').html(contentDialogs);
            chat.load.events();
            let dialog_id_selected = chat.cookie.get('dialog_id_selected');
            if($('[data-dialog-id="'+dialog_id_selected+'"]').length>0 && dialog_id_selected)
                $('[data-dialog-id="'+dialog_id_selected+'"]').click();

            // Продолжаем выполнение интервала
            isChatIntervalPaused = 0;
        },

        buildHTMLdialog: function(valuedialog){
            let last_name = '', first_name = '', manager_id = '', client_id = '', foto_url = '';

            if(valuedialog['banned'] && valuedialog['banned']==1){
                if(!SETT.MEMBER_ADMIN || SETT.MEMBER_ADMIN != 1){
                    return '';
                }
            }

            let count_unread_messages = '', unread = '', unreadHide = '';

            if(valuedialog['client_member'] && typeof valuedialog['client_member'] === 'object' &&
                !Array.isArray(valuedialog['client_member']) &&
                valuedialog['client_member'] !== null || SETT.MEMBER_MANAGER || SETT.MEMBER_ADMIN){
                last_name = valuedialog['client_member']['last_name'];
                first_name = valuedialog['client_member']['first_name'];
                client_id = valuedialog['client_member']['id'];
                foto_url = valuedialog['client_member']['foto_url'];
                count_unread_messages = valuedialog['count_unread_messages_client'];

            }else if(valuedialog['manager_member'] && typeof valuedialog['manager_member'] === 'object' &&
                !Array.isArray(valuedialog['manager_member']) &&
                valuedialog['manager_member'] !== null){
                last_name = valuedialog['manager_member']['last_name'];
                first_name = valuedialog['manager_member']['first_name'];
                manager_id = valuedialog['manager_member']['id'];
                let dialog_name = last_name + ' ' +first_name;
                count_unread_messages = valuedialog['count_unread_messages_manager'];

            }

            if(SETT.MEMBER_ADMIN){
                last_name = valuedialog['client_member']['last_name'];
                first_name = valuedialog['client_member']['first_name'];
                client_id = valuedialog['client_member']['id'];
                foto_url = valuedialog['client_member']['foto_url'];
                client_id = valuedialog['client_member_id'];
                manager_id = valuedialog['manager_member_id'];
                let dialog_name = last_name + ' ' +first_name;
                count_unread_messages = valuedialog['count_unread_messages_manager'];
            }


            if(!foto_url || foto_url.length < 1) {
                //foto_url = SETT.URL_SITE + '/templates/default/chat/assets/images/chat-nophoto.png';
                foto_url = 'https://online.systematika.org/public/img/default_profile_50.png';
            }


            if(count_unread_messages < 1 || !count_unread_messages){
                unreadHide = 'display: none;';
            }
            unread  = `<span style="background: #12B6F9; color: white; padding: 5px; border-radius: 25%; ${unreadHide}">${count_unread_messages}</span>`;



            let contentDialogGroups = '';



            valuedialog['groups'].forEach(group => {
                let programname = '';
                if(group['program']['name'])
                    programname = group['program']['name']+', ';

                let classname = '';
                if(group['class']['name'])
                    classname = group['class']['name']+', ';
                let grouptime = '';
                if(group['groupsync']['begin_date']){
                    let date = new Date(group['groupsync']['begin_date']);
                    let dayTxt = {0: 'воскресенье', 1: 'понедельник', 2: 'вторник', 3: 'Среда', 4: 'четверг', 5: 'пятница', 6: 'суббота'};
                    grouptime = dayTxt[date.getDay()] + ' в ' + date.getHours()+ ':' +
                        String(date.getMinutes()).padStart(2, '0'); //  + group['groupsync']['begin_date']
                }

                contentDialogGroups = `${programname}${classname}${grouptime}`;
            });


            let finaltime = '';
            if(valuedialog['lastmessage_time']){
                finaltime = chat.load.timeTpl(valuedialog['lastmessage_time']*1000);
            }


            if(SETT.MEMBER_ADMIN && valuedialog['calladmin']==1){
                last_name = '<b>'+last_name;
                first_name += '</b>';
            }


            return `<div class="row sideBar-body" 
                        data-dialog-manager-id="${manager_id}" 
                        data-dialog-client-id="${client_id}"
                        data-dialog-id="${valuedialog['dialog_id']}"
                        data-dialog-name="${valuedialog['dialog_name']}"
                        data-dialog-foto="${foto_url}"
                        data-dialog-banned="${valuedialog['banned']}"
                        data-dialog-calladmin="${valuedialog['calladmin']}"
                        >
                        <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="${foto_url}">
                            </div>
                        </div>
                        <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                                    <span class="name-meta">${last_name} ${first_name}</span><br/>
                                        <p class="name-group-meta">${contentDialogGroups}</p>

                                </div>
                                <div class="col-sm-2 col-xs-2 pull-right sideBar-time">
                                    <span class="time-meta pull-right">${finaltime}</span>
                                </div>
                                <div class="col-sm-2 col-xs-2 pull-right sideBar-time">
                                    <span class="unread-meta pull-right">${unread}</span>
                                </div>
                            </div>
                        </div>
                    </div>`;
        },

        dialogsUserOrTeacher: function(){
            isChatIntervalPaused = 1;
            chat.load.getLoadDialogsAjax();
        },

        // Интерфейс диалогов для админов
        dialogsAdmin: {
            isTeacherLoaded: 0,
            teachersData: {},
            init: function () {



               /* if(chat.load.dialogsAdmin.isTeacherLoaded<1)
                    this.loadTeachers();

                if(chat.cookie.get('mk_manager_id') && chat.load.dialogsAdmin.isTeacherLoaded>0)
                   */
               this.getLoadDialogs();



            },

            loadTeachers: function(){



                // Останавливаем интервал
                isChatIntervalPaused = 1;

                getLoadTeachers();
                function getLoadTeachers() {
                    $.ajax({
                        url: SETT.URL_SITE + '/chat/teachers-ajax/',
                        type: 'GET',
                        async: true,
                        dataType: 'html',
                        success: function (response) {
                            console.log('фффффф');
                            //console.log(response);
                            response = JSON.parse(response);
                            //console.log(response['teachers']);
                            let teachers = response['teachers'];

                            let contentTeachers = '', access = 0;
                            for (const [key, valueteacher] of Object.entries(teachers)) {
                                contentTeachers += `<div class="row sideBar-body" onclick="chat.load.dialogsAdmin.selectTeacher(this);" data-teacher-id="${valueteacher['id']}" data-mk-manager-id="${valueteacher['mk_manager_id']}">
                        <div class="col-sm-3 col-xs-3 sideBar-avatar" >
                            <div class="avatar-icon">
                                <img src="https://online.systematika.org/public/img/default_profile_50.png">
                            </div>
                        </div>
                        <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                  <span class="name-meta">${valueteacher['first_name']} ${valueteacher['last_name']}
                </span>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                  <span class="time-meta pull-right"></span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                            }
                            $('#teachersContent').html(contentTeachers);

                            chat.load.dialogsAdmin.isTeacherLoaded = 1;
                            $('#buttonTeachers').show();
                            console.log('открыто');
                            // Продолжаем выполнение интервала
                            isChatIntervalPaused = 0;
                        }
                    });
                }

            },
            getLoadDialogs: function (){
                isChatIntervalPaused = 1;

                chat.load.getLoadDialogsAjax();
            },

            selectTeacher: function (el) {
                chat.cookie.set('manager_member_id', $(el).data('teacher-id'));
                chat.cookie.set('mk_manager_id', $(el).data('mk-manager-id'));
                chat.cookie.set('new_select_teacher', 1);
                $('#buttonTeachers').html('<i class="fa fa-circle-o-notch fa-2x fa-spin"></i>');
                this.getLoadDialogs($(el).data('teacher-id'), 1);
                $('.newMessage-back').click();
                $('#chatDialogs').html('');
                console.log($(el).data('teacher-id'));
            }
        },

        messages: function (id) {
            let foto_url = $(id).data('dialog-foto');
            $('.heading-avatar-icon#dialog-avatar').html(`<a href="#" class="arrowBack" onclick="$('.side').show();"><i class="fa fa-arrow-left arrowBackIcon" aria-hidden="true"></i></a><img src="${foto_url}">`);
            $('.heading-name-meta').text($(id).data('dialog-name'));

            $('#calladmin').html('');
            $('#banunban').html('');

            if(SETT.MEMBER_ADMIN || SETT.MEMBER_MANAGER){
                if($(id).attr('data-dialog-calladmin')>0){
                    $('#calladmin').html('Позвали администратора');
                    if(SETT.MEMBER_ADMIN)
                        $('#calladmin').append(' <a href="#" style="background: #00bfa5; padding: 4px; color: white; border-radius: 5px;" onclick="chat.load.uncallAdmin('+$(id).data('dialog-id')+')">Вопрос решен</a>');
                }else if(SETT.MEMBER_MANAGER){
                    $('#calladmin').html('<a href="#" onclick="chat.load.callAdmin('+$(id).data('dialog-id')+')">Позвать администратора</a>');
                }
            }
            $('.row.sideBar-body').removeClass('active');
            $(id).addClass('active');


            this.getMessages($(id).data('dialog-manager-id'), $(id).data('dialog-client-id'));

            if($(id).data('dialog-banned')){
                if(SETT.MEMBER_ADMIN)
                    $('#banunban').html(' <a href="#" style="background: #15bf0d; padding: 4px; color: white; border-radius: 5px;" onclick="alert(`Пока только через БД`);">Разбанить чат</a>');
                $('.reply').hide();
            }else{
                if(SETT.MEMBER_ADMIN)
                    $('#banunban').html(' <a href="#" style="background: #bf3736; padding: 4px; color: white; border-radius: 5px;" onclick="alert(`Пока только через БД`);">Забанить чат</a>');
                $('.reply').show();
            }

            if($('.app-one').width() > 767 && $('.side').is(':hidden')){
                $('.side').show();
                $('.arrowBack').hide();
            }

        },

        getMessages: function (manager_id = 0, client_id = 0) {


            $.ajax({
                url: SETT.URL_SITE + '/chat/messages-ajax/',
                type: 'get',
                data: {'manager_id': manager_id, 'client_id': client_id},
                dataType: 'html',
                success: function (response) {
                    //console.log(response);
                    response = JSON.parse(response);


                    let messagesHtml = '', sender = '', senderText = '';
                    response['messages'].forEach(message => {
                        if(message['from_member_id']==SETT.MEMBER_ID){
                            sender = 'sender';
                        }else{
                            sender = 'receiver';
                        }
                        if(SETT.MEMBER_ADMIN){
                            if(message['from_member_id'] == client_id){
                                senderText = 'Клиент: ';
                            }else if(message['from_member_id'] == manager_id){
                                senderText = 'Преподаватель: ';
                            }else{
                                senderText = 'Администратор: ';
                            }
                        }

                        let time = chat.load.timeTpl(message['time'] * 1000);
                        let messageText = message['message'];

                        if(message['attachment_id'])
                            messageText = '<a href="'+SETT.URL_SITE+'/chat/attach/'+message['id']+'">'+messageText+'</a>';
                        messagesHtml += `<div class="row message-body">
                    <div class="col-sm-12 message-main-${sender}">
                        <div class="${sender}">
                            <div class="message-text">
                                ${senderText} ${messageText}
                            </div>
                            <span class="message-time pull-right">${time}</span>
                            
                
                        </div>
                    </div>
                </div>`;
                    });
                    $('[name="dialog_id"]').val(response['dialog_id']);



                    var div = $("#conversation"), maxScroll = document.getElementById('conversation').scrollHeight - document.getElementById('conversation').clientHeight;
                    let nowScroll = div.scrollTop();
                    $('#dialogMessages').html(messagesHtml);

                    if(nowScroll == maxScroll || manager_id != $('[name="manager_id"]').val()){
                        div.scrollTop(div.prop('scrollHeight'));
                    }

                    $('[name="manager_id"]').val(manager_id);
                    $('[name="client_id"]').val(client_id);


                }
            });
        },

        updateMessages: function(clearInput = 1){
            chat.load.getMessages($('[name="manager_id"]').val(), $('[name="client_id"]').val());
            if(clearInput)
                $('[name="message"]').val('');

            let div = $("#conversation");
            div.scrollTop(div.prop('scrollHeight'));
            $('#dialogFormMessage').focus();
        },

        sendMessage: function () {
            //alert('test'+$('[name="message"]').val());
            if(!$('[name="message"]').val())
                return;
            let spinner = '<i class="fa fa-circle-o-notch fa-2x fa-spin"></i>';
            let label = $('.reply-send');
            let labelvalue = label.html();
            //input.attr('disabled','disabled');;
            label.html(spinner);

            let dialog_id = $('[name="dialog_id"]').val();
            $.ajax({
                url: SETT.URL_SITE + '/chat/message-ajax/',
                type: 'post',
                data: {'dialog_id': dialog_id, 'message': $('[name="message"]').val()},
                dataType: 'html',
                success: function (response) {
                    //console.log(response);
                    /*chat.load.getMessages($('[name="manager_id"]').val(), $('[name="client_id"]').val());
                    $('[name="message"]').val('');
                    let div = $("#conversation");
                    div.scrollTop(div.prop('scrollHeight'));*/
                    chat.load.updateMessages();
                    label.html(labelvalue);

                    let dialog_id_el = $('[data-dialog-id="'+dialog_id+'"]');
                    $('#chatDialogs').prepend(dialog_id_el.clone());
                    dialog_id_el.remove();
                    chat.load.events();

                }
            });
        },

        sendFile: function(){
            //alert($('#message_file')[0].files[0].name);
            //  <!--class="fa fa-circle-o-notch fa-2x fa-spin"-->
            let spinner = '<i class="fa fa-2x" id="label_message_file_progress">0%</i>';
            let input = $('#message_file');
            let label = $('#label_message_file');

            let labelvalue = label.html();
            let dialog_id = $('[name="dialog_id"]').val();
            if (window.FormData === undefined) {
                alert('В вашем браузере FormData не поддерживается')
            } else {
                input.attr('disabled','disabled');;
                label.html(spinner);
                var formData = new FormData();
                let filedata = $("#message_file")[0].files[0];
                if(!filedata)
                    return;
                formData.append('file', filedata);
                formData.append('dialog_id', dialog_id);

                function setProgress(e) {
                    if (e.lengthComputable) {
                        var complete = e.loaded / e.total;
                        $("#label_message_file_progress").text(Math.floor(complete*100)+"%");
                    }
                }
                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", setProgress, false);
                        xhr.addEventListener("progress", setProgress, false);
                        return xhr;
                    },
                    type: "POST",
                    url: SETT.URL_SITE + '/chat/file-ajax/',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType : 'html',
                    success: function(msg){
                        //console.log(msg);
                        /*chat.load.updateMessages(false);*/
                        input.val('');
                        input.removeAttr('disabled');
                        label.html(labelvalue);
                    }
                });
            }

        },

        callAdmin: function(dialog_id){

            //console.log(dialog_id);

            $.ajax({
                type: "POST",
                url: SETT.URL_SITE + '/chat/call-admin-ajax/',
                data: {'dialog_id': dialog_id, 'call': 1},
                dataType : 'html',
                success: function(msg){
                    $('[data-dialog-id="'+dialog_id+'"]').attr('data-dialog-calladmin', '1');
                    //$('#calladmin').html('Позвали администратора');
                }
            });

        },

        uncallAdmin: function(dialog_id){
            $.ajax({
                type: "POST",
                url: SETT.URL_SITE + '/chat/call-admin-ajax/',
                data: {'dialog_id': dialog_id, 'call': 0},
                dataType : 'html',
                success: function(msg){
                    $('[data-dialog-id="'+dialog_id+'"]').attr('data-dialog-calladmin', '0');
                    $('[data-dialog-id="'+dialog_id+'"] span.name-meta').css('font-weight', 'normal');
                    //$('#calladmin').html('');
                }
            });
        },

        timeTpl: function (time) {
            timenow = new Date();
            timeneed = new Date(time);
            month = timeneed.toLocaleString('default', {month: 'short'});
            day = timeneed.getDate();
            let finaltime = '';
            if(day == timenow.getDate()){
                let minutes = (timeneed.getMinutes() < 10 ? '0' : '') + timeneed.getMinutes();
                finaltime = timeneed.getHours()+':'+minutes;

            }else {
                finaltime = timeneed.getDate() + ' ' + month;
            }
            return finaltime;
        }


    },

    test: function() { alert(screen.width); }

};
chat.init();