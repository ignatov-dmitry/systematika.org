<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')
    <title>Интеграция GetCourse и МойКласс</title>
</head>
<body class="dark:bg-gray-700">
@include('layouts.navigation')
<section id="content">
    @yield('content')
</section>
</body>
</html>
