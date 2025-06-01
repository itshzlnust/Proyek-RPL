<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Kulitku Skincare') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #8a2be2;  /* Purple color */
            --primary-light: #b19cd9;
            --primary-dark: #4b0082;
            --white: #ffffff;
            --gray-light: #f8f9fa;
            --gray: #e9ecef;
        }
        
        body {
            background-color: var(--gray-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .bg-primary {
            background-color: var(--primary) !important;
        }
        
        .text-primary {
            color: var(--primary) !important;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .sidebar {
            min-height: 100vh;
            background-color: var(--white);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 0.8rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--gray);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary);
            color: white;
        }
        
        .topbar {
            background-color: var(--white);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .content {
            padding: 1.5rem;
        }
        
        .card {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray);
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        
        .dashboard-card {
            border-left: 4px solid var(--primary);
        }
        
        .chart-container {
            height: 300px;
        }

        .sidebar-logo {
            width: 120px;
            height: auto;
            margin-bottom: 0.5rem;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Kulitku Skincare Logo" class="sidebar-logo">
                        <h5 class="text-primary">Beauty Clinic</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('treatments*') ? 'active' : '' }}" href="{{ route('treatments.index') }}">
                                <i class="fas fa-spa me-2"></i> Treatments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                <i class="fas fa-users me-2"></i> Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i> Orders
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form">
                                @csrf
                                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Topbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom topbar">
                    <h1 class="h3">@yield('title')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name ?? 'Admin' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="dropdown-item" id="dropdown-logout-form">
                                        @csrf
                                        <button type="submit" class="border-0 bg-transparent p-0">
                                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <!-- Content -->
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Handle logout forms to prevent CSRF token issues
        $(document).ready(function() {
            $('#sidebar-logout-form button, #dropdown-logout-form button').on('click', function(e) {
                e.preventDefault();
                
                // Create a new form
                var form = $('<form action="{{ route("logout") }}" method="POST">' +
                             '@csrf' +
                             '</form>');
                
                // Append to body and submit
                $('body').append(form);
                form.submit();
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html> 