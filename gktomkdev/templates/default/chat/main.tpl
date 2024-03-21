<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

<!------ Include the above in your HEAD tag ---------->
<script>
    var SETT = {
        URL_SITE: '{*URL_SITE*}',
        MEMBER_ID: '{*MEMBER.id*}',
        MEMBER_FIRST_NAME: '{*MEMBER.first_name*}',
        MEMBER_LAST_NAME: '{*MEMBER.last_name*}',
        MEMBER_FOTO_URL: '{*MEMBER.foto_url*}',
        {?*MEMBER.access*}MEMBER_ADMIN: '1',{?}
        {?*MEMBER.mk_manager_id*}MEMBER_MANAGER: '{*MEMBER.mk_manager_id*}',{?}
    };
</script>
<script src="{*URL_SITE*}/templates/{*TPL_NAME*}/chat/assets/js/chat.js?v=3.0"></script>
<link rel="stylesheet" href="{*URL_SITE*}/templates/{*TPL_NAME*}/chat/assets/css/style.css?v=4">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container-fluid app">
    <div class="row app-one">
        <div class="col-sm-4 side">
            <div class="side-one">
                <div class="row heading">
                    <div class="col-sm-3 col-xs-3 heading-avatar">
                        /*
                        <div class="heading-avatar-icon">
                            <img src="https://online.systematika.org/public/img/default_profile_50.png">
                        </div>
                        */
                    </div>
                    /*
                    <div class="col-sm-1 col-xs-1  heading-dot  pull-right">
                        <i class="fa fa-ellipsis-v fa-2x  pull-right" aria-hidden="true"></i>
                    </div>
                    */
                    <div id="buttonTeachers" class="col-sm-2 col-xs-2 heading-compose  pull-right"
                         style="display: none;">
                        <i class="fa fa-comments fa-2x  pull-right" aria-hidden="true"></i>
                    </div>
                </div>

                /*
                <div class="row searchBox">
                    <div class="col-sm-12 searchBox-inner">
                        <div class="form-group has-feedback">
                            <input id="searchText" type="text" class="form-control" name="searchText"
                                   placeholder="Search">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                */

                <div class="row sideBar scrollbar">
                    <div class="row sideBar-body"
                         onclick="window.open('https://online.systematika.org/pl/talks/conversation', '_blank');">
                        <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                                <img src="https://fs-thb01.getcourse.ru/fileservice/file/thumbnail/h/75f26f1926e987be50141f0322f54bb9.png/s/x50/a/22153/sc/58">
                            </div>
                        </div>
                        <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                                    <span class="name-meta"><b>Поддержка Систематики</b></span>
                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                                </div>
                            </div>
                        </div>
                    </div>


                    <p id="chatDialogs">
                        /*
                    <div class="row sideBar-body">
                        <div class="col-sm-3 col-xs-3 sideBar-avatar">
                            <div class="avatar-icon">
                            </div>
                        </div>
                        <div class="col-sm-9 col-xs-9 sideBar-main">
                            <div class="row">
                                <div class="col-sm-8 col-xs-8 sideBar-name">
                                    <span class="name-meta">Загрузка...</span>

                                </div>
                                <div class="col-sm-4 col-xs-4 pull-right sideBar-time">
                  <span class="time-meta pull-right">
                </span>


                                </div>
                            </div>
                        </div>
                    </div>
                    */
                    </p>

                    /**/


                </div>
            </div>

            <div class="side-two">
                <div class="row newMessage-heading">
                    <div class="row newMessage-main">
                        <div class="col-sm-2 col-xs-2 newMessage-back">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        </div>
                        <div class="col-sm-10 col-xs-10 newMessage-title">
                            Преподаватели
                        </div>
                    </div>
                </div>

                /*
                <div class="row composeBox">
                    <div class="col-sm-12 composeBox-inner">
                        <div class="form-group has-feedback">
                            <input id="composeText" type="text" class="form-control" name="searchText"
                                   placeholder="Search People">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                */

                <div class="row compose-sideBar scrollbar" id="teachersContent"></div>


            </div>
        </div>

        <div class="col-sm-8 conversation">
            <div class="row heading">
                <div class="col-sm-2 col-md-1 col-xs-3 heading-avatar">
                    <div class="heading-avatar-icon" id="dialog-avatar">
                    </div>
                </div>
                <div class="col-sm-8 col-xs-7 heading-name">
                    <a class="heading-name-meta"></a>
                    <span id="banunban" style="color: #778072;padding: 0 5;"></span>
                    <span id="calladmin" style="color: #778072;padding: 0 5;"></span>
                    <span class="heading-online">Online</span>


                </div>

            </div>

            <div class="row message scrollbar" id="conversation">
                /*
                <div class="row message-previous">
                    <div class="col-sm-12 previous">
                        <a onclick="previous(this)" id="ankitjain28" name="20">
                            Показать старые сообщения
                        </a>
                    </div>
                </div>
                */

                <p id="dialogMessages"></p>

                /*
                <div class="row message-body">
                    <div class="col-sm-12 message-main-receiver">
                        <div class="receiver">
                            <div class="message-text">
                                Привет, что делаешь?!
                            </div>
                            <span class="message-time pull-right">
                Пт
              </span>
                        </div>
                    </div>
                </div>

                <div class="row message-body">
                    <div class="col-sm-12 message-main-sender">
                        <div class="sender">
                            <div class="message-text">
                                Я валяюсь без дела!
                            </div>
                            <span class="message-time pull-right">
                Пт
              </span>
                        </div>
                    </div>
                </div>
                */


            </div>

            <div class="row reply" style="display: none;">
                <div class="col-sm-1 col-xs-1 reply-emojis">
                    /*<i class="fa fa-smile-o fa-2x"></i>*/
                </div>
                <div class="col-sm-9 col-xs-9 reply-main">
                    <input type="hidden" name="dialog_id" value="">
                    <input type="hidden" name="manager_id" value="">
                    <input type="hidden" name="client_id" value="">
                    <input type="file" multiple id="message_file" name="message_file[]" value="" style="display: none;">

                    <textarea class="form-control" rows="1" name="message" id="dialogFormMessage" required="required"></textarea>
                </div>
                <label id="label_message_file" for="message_file" class="col-sm-1 col-xs-1 reply-recording">
                    <i class="fa fa-file fa-2x" aria-hidden="true" id="label_message_file_progress"></i>
                </label>

                <div class="col-sm-1 col-xs-1 reply-send" onclick="chat.load.sendMessage();">
                    <i class="fa fa-send fa-2x" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="popup"><span class="close">&times;</span><img src=""></div>
<style>
    .popup {
        display: none;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: 35px 90px;
        height: auto;
        width: auto;
        position: fixed;
        z-index: 999;
        padding: 20px;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        overflow-y: auto;
        max-width: max-content;
    }

    .popup img {
        max-width: 100%;
        height: auto;
    }

    .close {
        position: absolute;
        top: 10px;
        left: 97%;
        font-size: 24px;
        cursor: pointer;
    }
</style>
<script>
    $(document).ready(function(){
        $(document).on('click', '.thumbnail', function(){
            console.log($(this).data('attachment-id'))
            $(".popup").show();
            $(".popup img").attr('src', $(this).attr('src'));
        });

        $(document).on('click', '.close', function(){
            $(".popup").hide();
        });
    });
</script>