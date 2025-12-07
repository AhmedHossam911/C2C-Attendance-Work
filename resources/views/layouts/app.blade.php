<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C2C Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('c2c logo Color.png') }}" type="image/png">
    <style>
        .navbar-dark .navbar-nav .nav-link {
            color: #ffffff !important;
        }

        .navbar-dark .navbar-brand {
            color: #ffffff !important;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <nav style="background-color: #14b8a6;" class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('c2c logo.png') }}" alt="C2C Logo" class="logo-img">
                <span>C2C Attendance</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('committees.index') }}">Committees</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('sessions.index') }}">Sessions</a>
                            </li>
                        @endif
                        @if (Auth::user()->hasRole('top_management'))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    Users
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('users.index') }}">All Users</a></li>
                                    <li><a class="dropdown-item" href="{{ route('users.pending') }}">Pending
                                            Approvals</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('authorizations.index') }}">HR Access</a>
                            </li>
                        @endif
                        @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('scan.index') }}">Scan QR</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    Reports
                                </a>
                                <ul class="dropdown-menu">
                                    @if (Auth::user()->hasRole('top_management') || Auth::user()->hasRole('board') || Auth::user()->hasRole('hr'))
                                        <li><a class="dropdown-item" href="{{ route('reports.index') }}">Committee
                                                Reports</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('reports.member') }}">Member Search</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }} ({{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }})
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4 flex-grow-1">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <footer style="background-color: #14b8a6;" class="text-center text-lg-start mt-auto py-3">
        <div class="container text-center">
            <p class="text-white">© {{ date('Y') }} C2C Attendance System. All rights reserved.</p>
            <p class="text-white"> Developed by <a href="https://linktr.ee/Ahmed_911" target="_blank"
                    style="color: #1e3b8a; font-weight: bold; text-decoration: none;">Ahmed Hossam</a> C2C
                President💙.
            </p>
        </div>
        <div class="container text-center mt-2">
            <a target="_blank" href="https://www.facebook.com/C2C.BIS.Helwan"
                class="text-white me-3 text-decoration-none footer-social-link"><i
                    class="bi bi-facebook fs-5"></i></a>
            <a target="_blank" href="https://www.tiktok.com/@c2c.bis.helwan"
                class="text-white me-3 text-decoration-none footer-social-link"><i class="bi bi-tiktok fs-5"></i></a>
            <a target="_blank" href="https://www.instagram.com/c2c.bis.helwan/"
                class="text-white me-3 text-decoration-none footer-social-link"><i
                    class="bi bi-instagram fs-5"></i></a>
            <a target="_blank" href="https://www.linkedin.com/company/c2c-bis-helwan/"
                class="text-white text-decoration-none footer-social-link"><i class="bi bi-linkedin fs-5"></i></a>
        </div>
    </footer>
</body>

</html>
