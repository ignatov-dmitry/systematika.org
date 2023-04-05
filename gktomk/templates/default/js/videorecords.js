let videorecords = {

    init: function () {

        $('#buttonRedownload').on('click', function () {
            videorecords.redownload.action();
        });

        videorecords.view.init();
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

            var re = '-';
            var str = date;
            var newstr = str.replaceAll(re, '/');
            let url = SETT.URL_SITE + '/zoom/video?v=videorecord/'+newstr+'/'+classname;
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

        }
    }

}

videorecords.init();