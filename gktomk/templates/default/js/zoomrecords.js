let zoomRecords = {

    init: function () {
        zoomRecords.view.init();
    },
    view: {
        init: function(){
            $('#modalView').on('hide.bs.modal', function () {
                zoomRecords.view.player.obj.api('pause');
            });
        },
        player: {id: '', obj: ''},
        openModal: function (id) {
            let tr = $('tr[data-id="'+id+'"]');
            let fileName = tr.data('filename');

            console.log(fileName);
            let url = SETT.URL_SITE + '/zoom/video?v=' + fileName;
            let url_prev = SETT.URL_SITE + "/templates/default/images/logo-new.png.webp";

            /*$('#player').html('<iframe src="'+url+'" width="100%" height="100%" border="0">\n' +
                '    Ваш браузер не поддерживает плавающие фреймы!\n' +
                ' </iframe>');*/

            //console.log(url);
            if(!zoomRecords.view.player.obj) {


                zoomRecords.view.player.id = id;
                zoomRecords.view.player.obj = new Playerjs({
                    id: "player",
                    file: url,
                    poster: url_prev
                });

            }else if(zoomRecords.view.player.id !== id){
                zoomRecords.view.player.obj.api('pause');
                zoomRecords.view.player.obj.api("play", url);
            }else if(zoomRecords.view.player.id === id){
                zoomRecords.view.player.obj.api('play');
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
                url: '/gktomk/videorecords/not-matched-safe/' + id,
                method: 'post',
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

zoomRecords.init();

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
    let tr = $('tr[data-id="'+id+'"]');
    let url = tr.data('filename');
    console.log(url);
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