"use strict";
var Homeworklinks = {

    init: function () {
        this.loadList.init();
    },

    loadList: {
        init: function () {
            this.load();
        },

        load: function () {
            $.ajax({
                url: SETT.URL_SITE + '/settings/homework-links-ajax/',
                method: 'get',
                success: function (response) {
                    Homeworklinks.loadList.listAll(response);
                }
            });
        },
        data: '',

        listAll: function (data) {

            this.data = JSON.parse(data);
            let programs = this.data['programs'];
            Homeworklinks.loadList.listPrograms(programs);


        },



        listPrograms: function (programs) {
            let output = '';
            programs.forEach(program => {

                console.log(program);
                output += `<div class="card">
            <div class="card-header" id="heading`+program['id']+`" data-id-program="`+program['id']+`" onclick="Homeworklinks.loadList.listProgramData(`+program['id']+`);">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse`+program['id']+`" aria-expanded="true" aria-controls="collapse`+program['id']+`">
                       `+program['name']+`
                    </button>
                </h2>
            </div>

            <div id="collapse`+program['id']+`" class="collapse" aria-labelledby="heading`+program['id']+`" data-parent="#accordionPrograms">
                <div class="card-body groups-content" id="programContent_`+program['id']+`">
                    Test
                </div>
            </div>
        </div>`;
            });

            $('#accordionPrograms').html(output);
        },

        updateListProgramData: function(id_program){
            $.ajax({
                url: SETT.URL_SITE + '/settings/homework-links-ajax/',
                method: 'get',
                success: function (data) {
                    Homeworklinks.loadList.data = JSON.parse(data);
                    Homeworklinks.loadList.listProgramData(id_program);
                }
            });
        },

        listProgramData: function (id_program) {
            console.log(this.data);

            let table = '<table class="table" id="table_'+id_program+'"><tr><th>Название</th><th>Группа</th><th>Ссылка</th><td></td></tr>';
            let rows = '';
            if(this.data['programs_data'][id_program]){
                this.data['programs_data'][id_program].forEach(homeworklink => {
                    let name = '', group = '', link = '';
                    if(homeworklink["name"]) name = homeworklink["name"];
                    if(homeworklink["group"]) group = homeworklink["group"];
                    if(homeworklink["link"]) link = homeworklink["link"];
                    rows += '<tr id="data_'+homeworklink["id"]+'">' +
                        '<input data-id-link="'+homeworklink["id"]+'" type="hidden" name="program_id" value="'+id_program+'">' +
                        '<td><input class="form-control form-control-sm input-form" onchange="Homeworklinks.loadList.saveProgramData('+homeworklink["id"]+');" data-id-link="'+homeworklink["id"]+'" type="text" name="name" value="'+name+'"></td>' +
                        '<td><input class="form-control form-control-sm input-form" onchange="Homeworklinks.loadList.saveProgramData('+homeworklink["id"]+');" data-id-link="'+homeworklink["id"]+'" type="text" name="group" value="'+group+'"></td>' +
                        '<td><input class="form-control form-control-sm input-form" onchange="Homeworklinks.loadList.saveProgramData('+homeworklink["id"]+');" data-id-link="'+homeworklink["id"]+'" type="text" name="link" value="'+link+'"></td>' +
                        '<td><a href="#!" onclick="Homeworklinks.loadList.deleteHomeworkLink('+homeworklink["id"]+', '+id_program+');"><i class="fas fa-trash-alt"></i></a></td></tr>';
                });
            }
            table += rows+'</table><center><a href="#!" id="addlink" onclick="Homeworklinks.loadList.addFieldInputs('+id_program+')"><i class="fas fa-plus-circle"></i> добавить</a></center>';

            $('#programContent_'+id_program).html(table);
        },

        addFieldInputs: function (id_program) {
            $('#addlink').html('<i class="fas fa-spinner"></i> загрузка')
            this.saveProgramDataByData({'program_id': id_program, 'name': 'Новая запись'});

         },

        saveProgramData: function (id_homeworklink) {
            let data = $('[data-id-link="'+id_homeworklink+'"]').serialize();
            data += '&id='+id_homeworklink;
            console.log(data);
            Homeworklinks.loadList.saveProgramDataByData(data);
        },

        saveProgramDataByData: function (data) {
            $.ajax({
                url: SETT.URL_SITE + '/settings/homework-links-ajax/',
                method: 'post',
                data: data,
                success: function () {
                    Homeworklinks.loadList.updateListProgramData(data['program_id']);
                }
            });
        },

        deleteHomeworkLink: function (id_homeworklink, id_program){
            $.ajax({
                url: SETT.URL_SITE + '/settings/homework-links-delete-ajax/',
                method: 'post',
                data: {'id': id_homeworklink},
                success: function () {
                    Homeworklinks.loadList.updateListProgramData(id_program);
                }
            });
        }
    }
};
Homeworklinks.init();