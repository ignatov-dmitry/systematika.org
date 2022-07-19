let addclass;
addclass = {

    openModal: function (email) {
        this.loadClasses();
        $('#modalAddclass').modal('show');
    },

    dataClasses: '',

    loadClasses: {

        init: function () {

        },

        dataClasses: '',

        load: function () {

            $.ajax({
                'url': SETT.URL_SITE + '/mk-classes',
                'type': 'GET',
                success: this.loadSuccess(response),

            });

        },

        loadSuccess: function (response) {
            data = JSON.parse(response);
            this.dataClasses = data;
            console.log(data);
            $('#result_div').html(response);
        },

        buildSelect: function () {
            this.dataClasses

            foreach(this.dataClasses)
        }
    }
};
