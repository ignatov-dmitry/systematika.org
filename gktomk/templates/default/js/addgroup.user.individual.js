var addgroupindividual = {
    init: function () {
        $("#formAddgroup").submit(
            function (event) {
                addgroupindividual.sendForm(); // Делаем отправку формы
                event.preventDefault(); // Убираем стандартную отправку формы
            }
        );
    },


};

addgroupindividual.init();