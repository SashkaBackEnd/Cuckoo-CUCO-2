<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer" />
    <title>Кукушка</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap&subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
<div id="root"></div>
<script src="{{mix('js/app.js')}}" ></script>
</body>
</html>
