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
                chat.load.sendFile();
            });

            $(".row.sideBar-body").click(function () {
                console.log($(this).data('dialog-manager-id'));
                chat.load.messages(this);
            });
        },

        managers: '',
        dialogs: function () {
            isChatIntervalPaused = 1;
            $.ajax({
                url: SETT.URL_SITE + '/chat/dialogs-ajax/',
                type: 'GET',
                async: true,
                dataType: 'html',
                success: function (response) {
                    response = JSON.parse(response);
                    console.log(response['dialogs']);
                    let dialogs = response['dialogs'];
                    this.managers = response['managers'];

                    let contentDialogs = '', access = 0;
                    for (const [key, value] of Object.entries(dialogs)) {

                        let last_name = '', first_name = '', manager_id = '', client_id = '', foto_url = '';

                        if(value['banned'] && value['banned']==1){
                            if(!SETT.MEMBER_ADMIN || SETT.MEMBER_ADMIN != 1){
                                continue;
                            }
                        }

                        if(value['client_member'] && typeof value['client_member'] === 'object' &&
                            !Array.isArray(value['client_member']) &&
                            value['client_member'] !== null){
                            last_name = value['client_member']['last_name'];
                            first_name = value['client_member']['first_name'];
                            client_id = value['client_member']['id'];
                            foto_url = value['client_member']['foto_url'];


                        }else if(value['manager_member'] && typeof value['manager_member'] === 'object' &&
                            !Array.isArray(value['manager_member']) &&
                            value['manager_member'] !== null){
                            last_name = value['manager_member']['last_name'];
                            first_name = value['manager_member']['first_name'];
                            manager_id = value['manager_member']['id'];
                            let dialog_name = last_name + ' ' +first_name;
                        }else
                            continue;

                        if(!foto_url || foto_url.length < 1)
                            foto_url = SETT.URL_SITE+'/templates/default/chat/assets/images/chat-nophoto.png';


                        let count_unread_messages = value['count_unread_messages'], unread = '';
                        if(count_unread_messages > 0){
                            unread  = `                <span class="time-meta pull-right"><span style="background: #12B6F9; color: white; padding: 5px; border-radius: 25%;">${count_unread_messages}</span>`;
                        }

                        let contentDialogGroups = '';

                        value['groups'].forEach(group => {
                            contentDialogGroups = `${group['program']['name']}, класс ${group['class']['name']}, четверг 19:00`;
                        });


                        let finaltime = '';
                        if(value['lastmessage_time']){
                            finaltime = chat.load.timeTpl(value['lastmessage_time']*1000);
                        }




                        contentDialogs += `<div class="row sideBar-body" 
                        data-dialog-manager-id="${manager_id}" 
                        data-dialog-client-id="${client_id}"
                        data-dialog-id="${value['dialog_id']}"
                        data-dialog-name="${value['dialog_name']}"
                        data-dialog-foto="${foto_url}"
                        data-dialog-banned="${value['banned']}"
                        data-dialog-calladmin="${value['calladmin']}"
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
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                                    <span class="time-meta pull-right">${finaltime}</span>
                                    ${unread}
                                </div>
                            </div>
                        </div>
                    </div>`;
                    }

                    $('#chatDialogs').html(contentDialogs);
                    chat.load.events();
                    let dialog_id_selected = chat.cookie.get('dialog_id_selected');
                    if($('[data-dialog-id="'+dialog_id_selected+'"]') && dialog_id_selected)
                        $('[data-dialog-id="'+dialog_id_selected+'"]').click();

                    // Продолжаем выполнение интервала
                    isChatIntervalPaused = 0;
                }
            });
        },

        messages: function (id) {
            let foto_url = $(id).data('dialog-foto');
            $('.heading-avatar-icon#dialog-avatar').html(`<img src="${foto_url}">`);
            $('.heading-name-meta').text($(id).data('dialog-name'));

            $('#calladmin').html('');
            if(SETT.MEMBER_ADMIN || SETT.MEMBER_MANAGER){
                if($(id).data('dialog-calladmin')==1){
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
            chat.cookie.set('dialog_id_selected', $(id).data('dialog-id'));
            if($(id).data('dialog-banned')){
                $('.reply').hide();
            }else{
                $('.reply').show();
            }

        },

        getMessages: function (manager_id = 0, client_id = 0) {
            $.ajax({
                url: SETT.URL_SITE + '/chat/messages-ajax/',
                type: 'get',
                data: {'manager_id': manager_id, 'client_id': client_id},
                dataType: 'html',
                success: function (response) {
                    console.log(response);
                    response = JSON.parse(response);


                    let messagesHtml = '', sender = '';
                    response['messages'].forEach(message => {
                        if(message['from_member_id']==SETT.MEMBER_ID){
                            sender = 'sender';
                        }else{
                            sender = 'receiver';
                        }
                        let time = chat.load.timeTpl(message['time'] * 1000);
                        let messageText = message['message'];

                        if(message['attachment_id'])
                            messageText = '<a href="chat/attach/'+message['id']+'" target="_blank">'+messageText+'</a>';
                        messagesHtml += `<div class="row message-body">
                    <div class="col-sm-12 message-main-${sender}">
                        <div class="${sender}">
                            <div class="message-text">
                                ${messageText}
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
                    console.log(response);
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
            let spinner = '<i class="fa fa-circle-o-notch fa-2x fa-spin"></i>';
            let input = $('#message_file');
            let label = $('#label_message_file');

            let labelvalue = label.html();

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

                $.ajax({
                    type: "POST",
                    url: SETT.URL_SITE + '/chat/file-ajax/',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType : 'html',
                    success: function(msg){
                        console.log(msg);
                        chat.load.updateMessages(false);
                        input.removeAttr('disabled');
                        label.html(labelvalue);
                    }
                });
            }

        },

        callAdmin: function(dialog_id){
            $('#calladmin').html('Позвали администратора');
            console.log(dialog_id);
        },

        uncallAdmin: function(dialog_id){
            console.log(dialog_id);
            $('#calladmin').html('');
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


    }

};
chat.init();