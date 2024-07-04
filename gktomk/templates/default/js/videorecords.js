
let videorecords = {
    init: function () {
        $('#buttonRedownload').on('click', function () {
            videorecords.redownload.action();
        });

        videorecords.view.init();
    },
    openUnassignedFolder: {
        videoRecordId: 0,
        openModal: function (id = 0) {
            this.videoRecordId = id;
            this.getVideoFolder();
            $('#modalUnassignedFolder').modal();
        },
        openDir: function (e, path = '') {
            e.preventDefault();
            this.getVideoFolder(path);
        },
        back: function (e, backUrl){
            e.preventDefault();
            this.getVideoFolder(backUrl);
        },
        selectVideo: function (e, name, path = ''){
            e.preventDefault();
            $.ajax({
                url: '/gktomk/videorecords/set-topic-name',
                method: 'post',
                dataType: 'json',
                data: {
                    record_id: this.videoRecordId,
                    name: name,
                    file_path: path + name
                },
                success: function(data){
                    alert('Видео выбрано');
                    $('#modalUnassignedFolder').modal('hide');
                }
            });
        },
        getVideoFolder: function (path = ''){
            $.ajax({
                url: '/gktomk/videorecords/video-folder',
                method: 'get',
                dataType: 'html',
                data: {path: path},
                success: function(data){
                    $('#folders').html(data)
                }
            });
        }
    },

    redownload: {

        record_id: 0,

        openModal: function (record_id) {
            this.record_id = record_id;
            $('#modalRedownload').modal();


        },

        action: function () {
            $('#buttonRedownload').text('Загрузка...');
            $.ajax({
                url: SETT.URL_SITE + '/videorecords/redownload/' + this.record_id,
                type: 'GET',
                dataType: 'html',
                success: function (response) {
                    console.log(response);
                    let $this = videorecords.redownload;
                    let status = '';
                    response = JSON.parse(response);
                    if(response['result'] == 'OK'){
                        status = 'OK';
                        $('[data-id-btnview="'+$this.record_id+'"]').removeClass('d-none');;
                    }else
                        status = response['result'];

                    if(response['record'] && response['record']['try_num'])
                        status = '<i>'+status+', попыток: '+response['record']['try_num']+'</i>';

                    $('[data-id-status="'+$this.record_id+'"]').html(status);
                    if(response['record'] && response['record']['meeting_topic'])
                        $('[data-id-meeting_topic="'+$this.record_id+'"]').html(response['record']['meeting_topic']);
                    $('#buttonRedownload').text('Да');
                }
            });
        }

    },

    view: {
        init: function(){
            $('#modalView').on('hide.bs.modal', function () {
                videorecords.view.player.obj.api('pause');
                /*$('#player').html('');*/
            });
        },
        player: {id: '', obj: ''},
        openModal: function (id) {
            let tr = $('tr[data-id="'+id+'"]');
            let date = tr.data('date');
            let classname = tr.data('class-name');
            let meeting_topic = tr.data('meeting-topic');
            let unassigned = tr.data('unassigned');
            let modify = tr.data('modify');
            let file_path = tr.data('file-path');

            let re = '-';
            let newstr = date.replaceAll(re, '/');

            let url = SETT.URL_SITE + '/zoom/video?v=videorecord/'+newstr+'/'+classname;

            if (unassigned){
                newstr = 'unassigned_videos/' + newstr;
                url = SETT.URL_SITE + '/zoom/video?v=videorecord/'+newstr+'/'+meeting_topic;
            }

            if (modify){
                url = SETT.URL_SITE + '/zoom/video?v=videorecord/'+file_path;
            }

            let url_prev = SETT.URL_SITE + "/templates/default/images/logo-new.png.webp";

            /*$('#player').html('<iframe src="'+url+'" width="100%" height="100%" border="0">\n' +
                '    Ваш браузер не поддерживает плавающие фреймы!\n' +
                ' </iframe>');*/

            //console.log(url);
            if(!videorecords.view.player.obj) {


                videorecords.view.player.id = id;
                videorecords.view.player.obj = new Playerjs({
                    id: "player",
                    file: url,
                    poster: url_prev
                });

            }else if(videorecords.view.player.id !== id){
                videorecords.view.player.obj.api('pause');
                videorecords.view.player.obj.api("play", url);
            }else if(videorecords.view.player.id === id){
                videorecords.view.player.obj.api('play');
            }

            $('#modalView').modal();

        },
        copyLink: function (id) {
            copyToClipboard(getEncryptedLink(id));
        },
        copyHtmlModal: function (id) {
            $('#copySafe').attr('data-video-id', id);
            $('#copyUnsafe').attr('data-video-id', id);

            $('#copyHtmlLink').modal();
        },
        copyHtmlSafe: function () {
            let id = $('#copySafe').attr('data-video-id');

            $.ajax({
                async: false,
                url: '/gktomk/videorecords/safe/' + id,
                method: 'get',
                dataType: 'json',
                success: function(data){
                    alert('Видео сохранено');
                }
            });

            copyToHtml(getEncryptedLink(id));
        },
        copyHtmlUnsafe: function () {
            let id = $('#copyUnsafe').attr('data-video-id');
            copyToHtml(getEncryptedLink(id));
        }
    }

}
videorecords.init();
async function copyTextToClipboard(text) {
    navigator.clipboard.writeText(text)
        .then(function() {
            console.log("Текст скопирован!");
        })
        .catch(function(err) {
            console.error("Ошибка при копировании: ", err);
        });
}

function copyToClipboard(text) {
    var textarea = document.createElement("textarea");
    textarea.value = text;

    textarea.style.position = "fixed";
    textarea.style.top = 0;
    textarea.style.left = 0;
    textarea.style.width = "2em";
    textarea.style.height = "2em";
    textarea.style.padding = 0;
    textarea.style.border = "none";
    textarea.style.outline = "none";
    textarea.style.boxShadow = "none";
    textarea.style.background = "transparent";

    document.body.appendChild(textarea);

    textarea.select();

    try {
        // Копируем текст в буфер обмена
        var successful = document.execCommand("copy");
        var msg = successful ? "Скопировано!" : "Не удалось скопировать текст!";
        alert(msg);
    } catch (err) {
        console.error("Ошибка при копировании: ", err);
    }
    document.body.removeChild(textarea);
}

function copyToHtml(link)
{
    var videoTag = '<video width="1024" controls><source src="' + link + '" type="video/mp4">Your browser does not support the video tag.</video>';

    $('#htmlCode').val(videoTag).select();

    try {
        // Копируем текст в буфер обмена
        var successful = document.execCommand('copy');;
        var msg = successful ? "Скопировано!" : "Не удалось скопировать текст!";
        alert(msg);
    } catch (err) {
        console.error("Ошибка при копировании: ", err);
    }

    //document.body.removeChild(tempTextarea);
}

function getEncryptedLink(id)
{
    let link = '';
    let tr = $('tr[data-id="'+id+'"]');
    let date = tr.data('date');
    let classname = tr.data('class-name');
    let meeting_topic = tr.data('meeting-topic');
    let unassigned = tr.data('unassigned');
    let modify = tr.data('modify');
    let file_path = tr.data('file-path');
    let re = '-';
    let newstr = date.replaceAll(re, '/');

    let url = 'videorecord/' + newstr + '/' + classname;

    // if (unassigned){
    //     newstr = 'unassigned_videos/' + newstr;
    //     classname = meeting_topic;
    // }

    if (unassigned){
        newstr = 'unassigned_videos/' + newstr;
        url = 'videorecord/' + newstr + '/' + meeting_topic;
    }

    if (modify){
        url = 'videorecord/' + file_path;
    }



    $.ajax({
        async: false,
        url: '/gktomk/watch/encrypt-link',
        method: 'get',
        dataType: 'json',
        data: {
            link: url
        },
        success: function(data){
            link = 'https://systematika.org/gktomk/watch?v=' + data;
        }
    });

    return link;
}