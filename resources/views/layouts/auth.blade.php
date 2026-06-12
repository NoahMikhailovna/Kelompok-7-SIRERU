<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SiReRu – Login</title>
    <link rel="stylesheet" href="{{ asset('css/sireru.css') }}">
</head>
<body>
    <div class="auth-bg">
        @yield('content')
    </div>
</body>
</html>
