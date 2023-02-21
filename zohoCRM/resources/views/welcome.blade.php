<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        
        <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}" >
    </head>
    <body class="antialiased">
        <form class="key_form" action="checkPin" method="POST">
            @csrf
            <label for="PIN">PIN:</label>
            <input type="password" id="PIN" name="pin" autofocus password required size="10">
            <input type="submit" value="Submit">
        </form>
    </body>
</html>
