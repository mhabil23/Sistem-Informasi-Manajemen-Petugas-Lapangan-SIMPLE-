<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk ke Sistem') - SIMPLE | BPS Kabupaten Kolaka Utara</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;850&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    <style>
        :root {
            --bps-orange: #F37021;
            --bps-orange-hover: #D95D12;
            --bps-orange-dark: #C2410C;
            --bps-orange-soft: #FFF4ED;
            --bps-orange-light: #FFEDE0;
            --bps-orange-subtle: #FFEDD5;
            --bps-orange-border: rgba(243, 112, 33, 0.28);
            --bps-orange-glow: rgba(243, 112, 33, 0.16);
            --bps-text-dark: #0F172A;
            --bps-text-muted: #64748B;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--bps-text-dark);
            background-color: #F8FAFC;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Background with BPS Warm Glow */
        .auth-background {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(circle at 10% 15%, rgba(243, 112, 33, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 90% 85%, rgba(243, 112, 33, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 244, 237, 0.6) 0%, #F8FAFC 100%);
            z-index: 0;
            pointer-events: none;
        }

        /* Ambient Decorative Glow Orbs */
        .auth-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.65;
            pointer-events: none;
        }
        .auth-orb-1 {
            width: 460px;
            height: 460px;
            background: radial-gradient(circle, rgba(243, 112, 33, 0.22) 0%, rgba(243, 112, 33, 0) 70%);
            top: -140px;
            left: -120px;
        }
        .auth-orb-2 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(254, 215, 170, 0.45) 0%, rgba(243, 112, 33, 0) 70%);
            bottom: -100px;
            right: -80px;
        }

        /* Main Shell Wrapper */
        .auth-main-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1040px;
            margin: auto;
            padding: 1.5rem 1rem;
        }

        /* Primary Bento Card */
        .auth-bento-card {
            background: #FFFFFF;
            border-radius: 28px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 
                0 25px 60px rgba(15, 23, 42, 0.07),
                0 4px 16px rgba(243, 112, 33, 0.05);
            overflow: hidden;
            display: flex;
            align-items: stretch;
            min-height: 520px;
            transition: all 0.3s ease;
        }

        /* Left Hero Showcase Pane (Desktop) */
        .auth-hero-pane {
            flex: 1 1 52%;
            background: linear-gradient(145deg, #FFFDFB 0%, #FFF7F0 45%, #FFEDE0 100%);
            padding: 3.25rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(243, 112, 33, 0.16);
            position: relative;
            overflow: hidden;
        }

        /* Right Form Pane */
        .auth-form-pane {
            flex: 1 1 48%;
            padding: 3.25rem 3.25rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #FFFFFF;
            position: relative;
        }

        /* Typography & Custom Elements */
        .text-bps-orange {
            color: var(--bps-orange) !important;
        }

        .btn-bps-primary {
            background: linear-gradient(135deg, #F37021 0%, #EA580C 100%);
            color: #FFFFFF !important;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(243, 112, 33, 0.32);
        }
        .btn-bps-primary:hover {
            background: linear-gradient(135deg, #E05D0E 0%, #C2410C 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(243, 112, 33, 0.42);
        }
        .btn-bps-primary:active {
            transform: translateY(0);
        }

        /* Custom Form Controls */
        .auth-input-group .input-group-text {
            background: #F8FAFC;
            border-color: #E2E8F0;
            color: #94A3B8;
            font-size: 1.1rem;
            border-top-left-radius: 14px;
            border-bottom-left-radius: 14px;
            padding-left: 1rem;
            padding-right: 0.75rem;
            transition: all 0.2s ease;
        }
        .auth-input-group .form-control {
            border-color: #E2E8F0;
            padding: 0.75rem 1rem 0.75rem 0.5rem;
            font-size: 0.92rem;
            color: #0F172A;
            border-top-right-radius: 14px;
            border-bottom-right-radius: 14px;
            transition: all 0.2s ease;
        }
        .auth-input-group .form-control:focus {
            border-color: var(--bps-orange);
            box-shadow: 0 0 0 3px rgba(243, 112, 33, 0.16);
        }
        .auth-input-group:focus-within .input-group-text {
            border-color: var(--bps-orange);
            color: var(--bps-orange);
            background: #FFF7ED;
        }

        /* Password Toggle Button */
        .auth-password-toggle {
            border-color: #E2E8F0;
            background: #F8FAFC;
            color: #94A3B8;
            border-top-right-radius: 14px !important;
            border-bottom-right-radius: 14px !important;
            padding-right: 1rem;
            transition: all 0.2s ease;
        }
        .auth-password-toggle:hover {
            color: var(--bps-orange);
            background: #FFF7ED;
        }
        .auth-input-group:focus-within .auth-password-toggle {
            border-color: var(--bps-orange);
        }

        /* Custom Checkbox */
        .form-check-input:checked {
            background-color: var(--bps-orange);
            border-color: var(--bps-orange);
        }
        .form-check-input:focus {
            border-color: var(--bps-orange);
            box-shadow: 0 0 0 3px rgba(243, 112, 33, 0.16);
        }

        /* Demo Buttons */
        .btn-demo-pill {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-demo-pill:hover {
            background: #FFF7ED;
            border-color: var(--bps-orange);
            color: var(--bps-orange);
            transform: translateY(-1px);
        }

        /* Responsive Mobile Styles (< 992px) */
        @media (max-width: 991.98px) {
            .auth-main-wrapper {
                max-width: 480px;
                padding: 1rem 0.75rem;
            }
            .auth-bento-card {
                flex-direction: column;
                border-radius: 24px;
                min-height: auto;
            }
            .auth-hero-pane {
                border-right: none;
                border-bottom: 1px solid rgba(243, 112, 33, 0.16);
                padding: 2.25rem 1.75rem 2rem;
            }
            .auth-form-pane {
                padding: 2rem 1.75rem 2.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-background">
        <div class="auth-orb auth-orb-1"></div>
        <div class="auth-orb auth-orb-2"></div>
    </div>

    <div class="auth-main-wrapper">
        @yield('content')
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
