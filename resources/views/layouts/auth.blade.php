<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - SIMPLE | Sistem Informasi Manajemen Petugas Lapangan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 20%, #0d2142 0%, #081326 90%);
            padding: 2rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .auth-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            pointer-events: none;
        }

        .auth-shape-1 {
            width: 450px;
            height: 450px;
            background: #0284c7;
            top: -100px;
            right: -100px;
        }

        .auth-shape-2 {
            width: 400px;
            height: 400px;
            background: #2563eb;
            bottom: -100px;
            left: -100px;
        }

        .auth-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            max-width: 460px;
            width: 100%;
            position: relative;
            z-index: 10;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-header {
            background: linear-gradient(135deg, #0d2142 0%, #132e5b 100%);
            padding: 2.25rem 2rem 1.75rem;
            text-align: center;
            color: #fff;
            position: relative;
        }

        .auth-logo {
            width: 70px;
            height: 70px;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.4));
        }

        .auth-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .auth-body {
            padding: 2rem 2.25rem 2.25rem;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-shape auth-shape-1"></div>
        <div class="auth-shape auth-shape-2"></div>

        <div class="auth-card">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

