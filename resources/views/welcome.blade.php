<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Chama</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container text-center mt-5">
        <h1>Smart Chama Funding and Contribution System</h1>
        <p class="lead">Manage your chama contributions and loans easily</p>
        
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
        @endauth
    </div>
</body>
</html>