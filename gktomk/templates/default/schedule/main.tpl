<!doctype html>
<html lang="ru">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>Расписание занятий</title>
    <script>var SETT = { URL_SITE: '{*URL_SITE*}' }; </script>

    <link rel="stylesheet" href="{*URL_SITE*}/templates/{*TPL_NAME*}/css/monthly.css">

</head>
<body style="background-color: #f5f5f5;">







<main role="main" class="container">

    {*CONTENT*}


</main>






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
<script type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/jquery.js"></script>
<script type="text/javascript" src="{*URL_SITE*}/templates/{*TPL_NAME*}/js/monthly.js"></script>
<script async type="text/javascript">

    var sampleEvents = {
        "monthly": [
            {
                "id": 1,
                "name": "Whole month event",
                "startdate": "2021-03-01",
                "enddate": "2021-03-31",
                "starttime": "12:00",
                "endtime": "2:00",
                "color": "#99CCCC",
                "url": ""
            },
            {
                "id": 2,
                "name": "Test encompasses month",
                "startdate": "2016-10-29",
                "enddate": "2016-12-02",
                "starttime": "12:00",
                "endtime": "2:00",
                "color": "#CC99CC",
                "url": ""
            },
            {
                "id": 3,
                "name": "Test single day",
                "startdate": "2016-11-04",
                "enddate": "",
                "starttime": "",
                "endtime": "",
                "color": "#666699",
                "url": "https://www.google.com/"
            },
            {
                "id": 8,
                "name": "Test single day",
                "startdate": "2016-11-05",
                "enddate": "",
                "starttime": "",
                "endtime": "",
                "color": "#666699",
                "url": "https://www.google.com/"
            },
            {
                "id": 4,
                "name": "Test single day with time",
                "startdate": "2016-11-07",
                "enddate": "",
                "starttime": "12:00",
                "endtime": "02:00",
                "color": "#996666",
                "url": ""
            },
            {
                "id": 5,
                "name": "Test splits month",
                "startdate": "2016-11-25",
                "enddate": "2016-12-04",
                "starttime": "",
                "endtime": "",
                "color": "#999999",
                "url": ""
            },
            {
                "id": 6,
                "name": "Test events on same day",
                "startdate": "2016-11-25",
                "enddate": "",
                "starttime": "",
                "endtime": "",
                "color": "#99CC99",
                "url": ""
            },
            {
                "id": 7,
                "name": "Test events on same day",
                "startdate": "2016-11-25",
                "enddate": "",
                "starttime": "",
                "endtime": "",
                "color": "#669966",
                "url": ""
            },
            {
                "id": 9,
                "name": "Test events on same day",
                "startdate": "2016-11-25",
                "enddate": "",
                "starttime": "",
                "endtime": "",
                "color": "#999966",
                "url": ""
            }
        ]
    };

    $(window).load( function() {
        $('#mycalendar').monthly({
            mode: 'event',
            //dataType: 'json',
           // events: sampleEvents
            //jsonUrl: 'http://z22627.adman.cloud/gktomk/events.json',
            jsonUrl: '{*URL_SITE*}/schedule/events-json?email={*GET_EMAIL*}',
            linkCalendarToEventUrl: false,
            dataType: 'json',
            weekStart: 'Mon',
            stylePast: true
        });
    });
</script>
</body>
</html>