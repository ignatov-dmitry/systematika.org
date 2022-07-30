<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    /*<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
*/
    <link rel="stylesheet" href="{*URL_SITE*}/templates/{*TPL_NAME*}/schedule/assets/css/style.css?v=14" >

    <title>Расписание занятий</title>
    <script>var SETT = { URL_SITE: '{*URL_SITE*}' }; </script>
    <script>var scheduleEmail = '{*GET_EMAIL*}';</script>

    <style>
        #Notify {
            position:fixed;
            width:auto;
            height:auto;
            min-width: 300px;
            top:30px;
            right:5px;
            z-index: 9999;
        }
    </style>
</head>
<body>




<div class="container">
    {*CONTENT*}
</div>


<div class="modal fade" id="modalEmpty" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div id="modalEmptyDialog" class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="emptyModalLabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>


            <span id="modalEmptyContent">            </span>

        </div>
    </div>
</div>

<style>
    button.close{
        padding:0;
        background-color:transparent;
        border:0
    }
    a.close.disabled{pointer-events:none}
    button{border-radius:0}
    button:focus{outline:1px dotted;outline:none -webkit-focus-ring-color}
    button,input,optgroup,select,textarea{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}button,input{overflow:visible}button,select{text-transform:none}[role=button]{cursor:pointer}select{word-wrap:normal}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button}[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){cursor:pointer}[type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner,button::-moz-focus-inner{padding:0;border-style:none}input[type=checkbox],input[type=radio]{box-sizing:border-box;padding:0}textarea{overflow:auto;resize:vertical}fieldset{min-width:0;padding:0;margin:0;border:0}legend{display:block;width:100%;max-width:100%;padding:0;margin-bottom:.5rem;font-size:1.5rem;line-height:inherit;color:inherit;white-space:normal}progress{vertical-align:baseline}[type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}[type=search]{outline-offset:-2px;-webkit-appearance:none}[type=search]::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{font:inherit;-webkit-appearance:button}

    .modal-header .close {
        padding: 0.7rem 0.7rem;
        margin: -1rem -1rem -1rem auto;
    }

    .close {
        float: right;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: .5;
    }

    .close:hover {
        color: #000;
        text-decoration: none;
    }
    .close:not(:disabled):not(.disabled):focus, .close:not(:disabled):not(.disabled):hover {
        opacity: .75;
    }
</style>


<script src="https://kit.fontawesome.com/b82fde3122.js" crossorigin="anonymous"></script>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script async>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()

    })
</script>
/* Всплывающие уведомления */
<div id="Notify"></div>
<script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/Notify.js"></script>

/* Подключаем скрипты для страницы расписания */
<script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/schedule.js?r={*@rand(1000000, 99999999)*}"></script>

<script defer type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/main.js?r={*@rand(1000000, 99999999)*}"></script>

<script src="https://kit.fontawesome.com/3c0bf4c975.js" crossorigin="anonymous"></script>
</body>
</html>