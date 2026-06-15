<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LawSphere') — Legal Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/lawsphere.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class=" navbar navbar-expand-lg navbar-dark lawsphere-nav">
        <div class="container">
            <a class=" navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-shield-check me-2"></i>LawSphere
            </a>
            <button class=" navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class=" navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class=" navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('lawyers.index') }}">
                            <i class="bi bi-search me-1"></i>Find a Lawyer
                        </a>
                    </li>
                    @auth
                        @if(auth()->user()->isClient())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.legal-advice.index') }}">
                                    <i class="bi bi-journal-text me-1"></i>Legal Advice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.appointments.index') }}">
                                    <i class="bi bi-calendar-check me-1"></i>Appointments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.chat.index') }}">
                                    <i class="bi bi-chat-dots me-1"></i>Messages
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('client.reviews.index') }}">
                                    <i class="bi bi-star me-1"></i>Reviews
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payments.index') }}">
                                    <i class="bi bi-credit-card me-1"></i>Payments
                                </a>
                            </li>
                        @elseif(auth()->user()->isLawyer() && auth()->user()->lawyer?->is_approved)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lawyer.legal-advice.index') }}">
                                    <i class="bi bi-journal-text me-1"></i>Legal Advice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lawyer.appointments.index') }}">
                                    <i class="bi bi-calendar-check me-1"></i>Appointments
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lawyer.chat.index') }}">
                                    <i class="bi bi-chat-dots me-1"></i>Messages
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('lawyer.membership.index') }}">
                                    <i class="bi bi-award me-1"></i>Membership
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payments.index') }}">
                                    <i class="bi bi-credit-card me-1"></i>Payments
                                </a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.lawyers.index', ['filter' => 'pending']) }}">
                                    <i class="bi bi-person-check me-1"></i>Lawyer Approvals
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route(auth()->user()->dashboard_route) }}">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('password.change') }}">Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-btn btn-btn-sm btn-btn-gold" href="#" data-bs-toggle="dropdown">Register</a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('register.client') }}">As Client</a></li>
                                <li><a class="dropdown-item" href="{{ route('register.lawyer') }}">As Lawyer</a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @if(session('status'))
            <div class="container mb-3">
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="container mb-3">
                <div class="alert alert-warning alert-dismissible fade show">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="container mb-3">
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="lawsphere-footer py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} LawSphere. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
