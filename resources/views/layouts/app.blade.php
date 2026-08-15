<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page-title', 'LetterFlow')</title>

    @vite('resources/css/app.css')

    @yield('page-css')
</head>
<body>
    @yield('content')
</body>
</html>