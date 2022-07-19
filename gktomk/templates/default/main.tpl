<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>Интеграция GetCourse и МойКласс</title>
    <script>var SETT = { URL_SITE: '{*URL_SITE*}' }; </script>

    <style>
        #notifies {
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
<body style="background-color: #f5f5f5;">




<nav class="navbar navbar-expand-lg navbar-dark rounded" style="background-color: #6f42c1;">

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample10" aria-controls="navbarsExample10" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample10">
        <a class="navbar-brand" href="{*URL_SITE*}/">Интеграция GetCourse и MoyKlass</a>
        <ul class="navbar-nav">
           /* <li class="nav-item">
                <a class="nav-link" href="{*URL_SITE*}/">Главная</a>
            </li> */
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown10" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Настройки</a>
                <div class="dropdown-menu" aria-labelledby="dropdown10">
                    /*<a class="dropdown-item" href="{*URL_SITE*}/sett-conn">Подключение</a>*/
                    <a class="dropdown-item" href="{*URL_SITE*}/settings/sync">Синхронизация</a>
                    <a class="dropdown-item" href="{*URL_SITE*}/subscriptions">Список абонементов</a>
                </div>
            </li>
        </ul>
    </div>
</nav>






<main role="main" class="container">

    {*CONTENT*}


</main>

<footer class="footer mt-auto py-3">
    <div class="container">
        <span class="text-muted">Version 1.0 | Powered by <a href="https://nekrasovonline.ru/" target="_blank">NekrasovOnline.RU</a>  © 2020</span>
    </div>
</footer>







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


<div id="notifies"></div>

<script>
    Notify = {
        TYPE_INFO: 0,
        TYPE_SUCCESS: 1,
        TYPE_WARNING: 2,
        TYPE_DANGER: 3,

        generate: function (aText, aOptHeader, aOptType_int) {
            var lTypeIndexes = [this.TYPE_INFO, this.TYPE_SUCCESS, this.TYPE_WARNING, this.TYPE_DANGER];
            var ltypes = ['alert-info', 'alert-success', 'alert-warning', 'alert-danger'];
            var ltype = ltypes[this.TYPE_INFO];

            if (aOptType_int !== undefined && lTypeIndexes.indexOf(aOptType_int) !== -1) {
                ltype = ltypes[aOptType_int];
            }

            var lText = '';
            if (aOptHeader) {
                lText += "<h4>"+aOptHeader+"</h4>";
            }
            lText += "<p>"+aText+"</p>";
            var lNotify_e = $("<div class='alert "+ltype+"' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button>"+lText+"</div>");

            setTimeout(function () {
                lNotify_e.alert('close');
            }, 3000);
            lNotify_e.appendTo($("#notifies"));
        }
    };



</script>
</body>
</html>