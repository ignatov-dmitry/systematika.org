var URL_GKTOMK = 'https://systematika.org/gktomk';
var mkTargetSelector = 'li[data-reactid=".0.1.1.0.1.4"]';
var mkSubjectSelector = '#moyklass_link';
var mkEmailSelector = '.user-email';
var mkGoAjax = 0;

function mkWidgetStart() {

    var interval = setInterval(function () {
        if (waitWindow()) {
            addLink();
        }
    }, 300);
}

// Функция проверяет наличение повяления окошка
function waitWindow() {
    if ($(mkTargetSelector).length) {
        return 1;
    } else {
        mkGoAjax = 0;
        return 0;
    }
}

// Проверяем, существует ли ссылка на мк
function checkLink() {
    if ($(mkSubjectSelector).length) {
        return 1;
    } else {
        return 0;
    }
}

// Функция добавляет ссылку
function addLink() {
    if (!checkLink()) {
        goAjaxCheckUser();
    }
}


function goAjaxCheckUser() {
    if(!mkGoAjax){
        mkGoAjax = 1;
        $.ajax({
            url: URL_GKTOMK + '/widget/' + $(mkEmailSelector).html(),
            method: 'get',
            dataType: 'json',
            success: function (resp) {
                if (resp['userId']) {
                    addLinkFinal();
                }
            }
        });
    }
}

function addLinkFinal() {
    $(mkTargetSelector).after('<li class="moyklass" id="moyklass_blank"></li>');
    $('#moyklass_blank').html('<a id="moyklass_link" href="' + URL_GKTOMK + '/widget/go/' + $(mkEmailSelector).html() + '" target="_blank" style="color: white;">MK</a><style>.moyklass {background-color: rgb(76,175,80);} .moyklass:hover { background-color: green; } </style>');
}

mkWidgetStart();

alert('Меня вызвали');
