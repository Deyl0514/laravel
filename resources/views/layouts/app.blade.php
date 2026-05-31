<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Task Manager</title>
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --primary-accent: #3b82f6;
            --secondary-accent: #10b981;
            --danger-accent: #ef4444;
            --warning-accent: #f59e0b;
            --text-light: #f8fafc;
            --border-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding: 0;
            color: var(--text-light);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            color: var(--primary-accent);
        }

        .sidebar-nav {
            padding: 15px 0;
        }

        .nav-link {
            color: rgba(248, 250, 252, 0.7);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .nav-link:hover {
            color: var(--text-light);
            background-color: rgba(59, 130, 246, 0.1);
            border-left-color: var(--primary-accent);
        }

        .nav-link.active {
            color: var(--text-light);
            background-color: rgba(59, 130, 246, 0.15);
            border-left-color: var(--primary-accent);
            font-weight: 600;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .user-profile {
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: auto;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-accent);
        }

        /* Main Content Styling */
        .main-wrapper {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: white;
            padding: 15px 30px;
            box-shadow: var(--border-shadow);
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-brand {
            color: var(--sidebar-bg) !important;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .content {
            flex: 1;
            padding: 30px;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--border-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--border-shadow);
            border-left: 4px solid var(--primary-accent);
            text-align: center;
        }

        .stat-card.users {
            border-left-color: #06b6d4;
        }

        .stat-card.tasks {
            border-left-color: #8b5cf6;
        }

        .stat-card.completed {
            border-left-color: var(--secondary-accent);
        }

        .stat-card.pending {
            border-left-color: var(--warning-accent);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--sidebar-bg);
            margin: 10px 0;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-card.users .stat-icon {
            color: #06b6d4;
        }

        .stat-card.tasks .stat-icon {
            color: #8b5cf6;
        }

        .stat-card.completed .stat-icon {
            color: var(--secondary-accent);
        }

        .stat-card.pending .stat-icon {
            color: var(--warning-accent);
        }

        /* Table Styling */
        .table {
            background: white;
        }

        .table thead th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
            color: #334155;
            font-weight: 600;
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badge Styling */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #cffafe;
            color: #164e63;
        }

        /* Button Styling */
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-accent);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            color: white;
        }

        .btn-success {
            background-color: var(--secondary-accent);
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
            color: white;
        }

        .btn-danger {
            background-color: var(--danger-accent);
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            color: white;
        }

        .btn-secondary {
            background-color: #64748b;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #475569;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        /* Form Styling */
        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
        }

        .form-label {
            color: #334155;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 12px 12px 0 0;
        }

        .modal-header .btn-close {
            filter: brightness(0.6);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
                margin-bottom: 20px;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .content {
                padding: 15px;
            }

            .stat-card {
                margin-bottom: 15px;
            }
        }

        /* Error and Success Messages */
        .alert {
            border-radius: 8px;
            border: none;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Overdue task styling */
        .task-overdue {
            border-left: 4px solid var(--danger-accent);
            background-color: #fef2f2;
        }

        /* Pagination */
        .pagination {
            gap: 5px;
        }

        .page-link {
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            color: var(--primary-accent);
        }

        .page-link:hover {
            background-color: var(--primary-accent);
            color: white;
            border-color: var(--primary-accent);
        }

        .page-link.active {
            background-color: var(--primary-accent);
            border-color: var(--primary-accent);
        }

        /* Toastr customization */
        .toast {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        /* Chart container */
        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Profile picture preview */
        .profile-picture-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-accent);
            margin-bottom: 15px;
        }

        /* Dropdown menu in sidebar */
        .dropdown-menu {
            background-color: rgba(59, 130, 246, 0.95);
            border: none;
            border-radius: 8px;
            margin-top: 5px;
        }

        .dropdown-item {
            color: var(--text-light);
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background-color: rgba(59, 130, 246, 0.8);
            color: var(--text-light);
        }

        /* Search and filter styling */
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-badge {
            display: inline-block;
            background-color: var(--primary-accent);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 10px;
        }

        /* Category chip */
        .category-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #eef2ff;
            color: #3730a3;
        }
        .category-chip .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Kanban */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        @media (max-width: 992px) {
            .kanban-board { grid-template-columns: 1fr; }
        }
        .kanban-column {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            min-height: 400px;
        }
        .kanban-column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 700;
            color: #334155;
        }
        .kanban-column .count {
            background: white;
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 0.8rem;
            color: #64748b;
        }
        .kanban-list {
            min-height: 100px;
        }
        .kanban-card {
            background: white;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            cursor: grab;
            border-left: 4px solid #cbd5e1;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .kanban-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-1px);
        }
        .kanban-card.priority-high { border-left-color: var(--danger-accent); }
        .kanban-card.priority-medium { border-left-color: var(--warning-accent); }
        .kanban-card.priority-low { border-left-color: var(--secondary-accent); }
        .kanban-card .title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .kanban-card .meta {
            display: flex;
            gap: 8px;
            align-items: center;
            font-size: 0.75rem;
            color: #64748b;
            flex-wrap: wrap;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: #e0e7ff !important;
        }
        .sortable-drag {
            transform: rotate(2deg);
        }

        /* Progress bar */
        .progress-thin {
            height: 8px;
            border-radius: 999px;
            background-color: #e2e8f0;
            overflow: hidden;
        }
        .progress-thin .bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-accent), var(--secondary-accent));
            transition: width 0.4s ease;
        }
    </style>

    @yield('extra-css')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-tasks" style="font-size: 1.5rem; color: var(--primary-accent);"></i>
                <h4>TaskMgr</h4>
            </div>

            <nav class="sidebar-nav flex-column">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                    <i class="fas fa-list-check"></i>
                    <span>Tasks</span>
                </a>
                <a href="{{ route('tasks.board') }}" class="nav-link {{ request()->routeIs('tasks.board') ? 'active' : '' }}">
                    <i class="fas fa-columns"></i>
                    <span>Board</span>
                </a>
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="{{ route('tasks.trashed') }}" class="nav-link {{ request()->routeIs('tasks.trashed') ? 'active' : '' }}">
                    <i class="fas fa-trash-can"></i>
                    <span>Trash</span>
                </a>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i>
                    <span>Profile</span>
                </a>

                <div class="user-profile">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ auth()->user()->getProfilePictureUrl() }}" alt="Profile" class="user-avatar">
                        <div>
                            <div style="font-size: 0.85rem; color: var(--text-light);">{{ auth()->user()->name }}</div>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="nav-link p-0 text-danger border-0" style="font-size: 0.8rem;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-wrapper w-100">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0">@yield('page-title')</span>
                    <div class="d-flex align-items-center gap-3">
                        <span style="color: #64748b; font-size: 0.9rem;">
                            <i class="fas fa-calendar-alt"></i>
                            {{ now()->format('l, F d') }}
                        </span>
                    </div>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SortableJS (drag-and-drop for Kanban) -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <!-- CSRF + global jQuery setup -->
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        function confirmDelete(title, text = "This action can be undone from Trash.") {
            return Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            });
        }
    </script>

    <!-- Toastr Configuration -->
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>

    @yield('extra-js')
</body>
</html>
