<!-- resources/views/layouts/sidebar.blade.php -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <h1 class="brand-text font-weight-light">Fionix</h1>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->avatar ?? asset('default-avatar.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if(Auth::user()->hasRole('admin'))
                    <li class="nav-item">
                        <a href="{{ url('admin/dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('admin/users/create') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Create User</p>
                        </a>
                    </li>
                @endif
                
                @if(Auth::user()->hasRole('supervisor'))
                    <li class="nav-item">
                        <a href="{{ url('supervisor/dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Active Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('supervisor/finished-projects') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>List of Finished Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('money-requests/create') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Create Money Request</p>
                        </a>
                    </li>
                @endif

                @if(Auth::user()->hasRole('manager'))
                    <li class="nav-item">
                        <a href="{{ url('manager/dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Active Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('manager/finished-projects') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>List of Finished Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('projects/create') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Create Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('manager/money-requests/create') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Create Money Request</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('manager/money-requests/pending') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Pending Money Request</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('manager/money-requests/all') }}" class="nav-link">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>All Money Request</p>
                        </a>
                    </li>
                @endif

                @if(Auth::user()->hasRole('owner'))
                    <li class="nav-item">
                        <a href="{{ url('owner/dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Pending Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('owner/active') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Active Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('owner/finished') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Finished Projects</p>
                        </a>
                    </li>
                @endif

                @if(Auth::user()->hasRole('account manager'))
                    <li class="nav-item">
                        <a href="{{ url('account-manager/dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Approved Money Out Transactions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('account-manager/active-projects') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Active Projects</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('account-manager/finished-projects') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>List of Finished Projects</p>
                        </a>
                    </li>
                @endif
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>
                            Balance: {{ isset($overallBalance) ? number_format($overallBalance, 2) : 'N/A' }}
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
