<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CRM - Online Course Academy')</title>
    
    <!-- Bootstrap CSS -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .language-switcher .dropdown-toggle::after {
            display: none;
        }
        .language-switcher .btn {
            border: none;
            background: transparent;
        }
        .language-switcher .btn:hover {
            background: rgba(255,255,255,0.1);
        }
        .bg-primary {
            background-color: #061450  !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('students.index') }}">
                <i class="fas fa-graduation-cap me-2"></i>
                CRM
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" 
                           href="{{ route('students.index') }}">
                            <i class="fas fa-users me-1"></i>
                            {{ __('common.students') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('follow-ups.*') ? 'active' : '' }}" 
                           href="{{ route('follow-ups.index') }}">
                            <i class="fas fa-comments me-1"></i>
                            {{ __('common.follow_ups') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" 
                           href="{{ route('classes.index') }}">
                            <i class="fas fa-chalkboard-teacher me-1"></i>
                            {{ __('common.classes') }}
                        </a>
                    </li>
                    @if(Auth::user() && (Auth::user()->isAdmin() || Auth::user()->isDepartmentManager()))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" 
                           href="{{ route('expenses.index') }}">
                            <i class="fas fa-file-invoice-dollar me-1"></i>
                            {{ __('expenses.expenses') }}
                        </a>
                    </li>
                    @endif
                        @if(Auth::user() && Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('expense-types.*') ? 'active' : '' }}" 
                               href="{{ route('expense-types.index') }}">
                                <i class="fas fa-list me-1"></i>
                                {{ __('expense_types.expense_types') }}
                            </a>
                        </li>
                        @endif
                        @if(Auth::user() && Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="fas fa-users-cog me-1"></i>
                                {{ __('common.users') }}
                            </a>
                        </li>
                        @endif
                </ul>
                
                @auth
                    <!-- Student Search -->
                    <form class="d-flex me-3" action="/students" method="GET">
                        <input class="form-control me-2" type="search" name="search" 
                               placeholder="{{ __('Search by phone or ID...') }}" aria-label="{{ __('common.search') }}"
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    
                    <!-- Language Switcher -->
                    <div class="language-switcher me-3">
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" 
                                    id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-globe me-1"></i>
                                {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" 
                                       href="{{ route('language.switch', 'en') }}">
                                        <i class="fas fa-flag-usa me-2"></i>
                                        English
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" 
                                       href="{{ route('language.switch', 'ar') }}">
                                        <i class="fas fa-flag me-2"></i>
                                        العربية
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- User Menu -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            {{ __('common.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-4">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-3">
        <div class="container text-center">
            <small class="text-muted">
                &copy; {{ date('Y') }} Online Course Academy CRM. All rights reserved.
            </small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    @stack('scripts')
</body>
</html>