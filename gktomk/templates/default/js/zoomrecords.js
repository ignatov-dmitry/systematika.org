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

        }
    }

}

zoomRecords.init();