@extends('layouts.app')

@section('title', 'Tasks Management')
@section('page-title', 'My Tasks')

@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control"
                       placeholder="Search tasks..." value="{{ request('search') }}">
                <select class="form-select" id="statusFilter" style="max-width: 150px;">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                </select>
                <select class="form-select" id="priorityFilter" style="max-width: 150px;">
                    <option value="">All Priority</option>
                    <option value="low" @selected(request('priority') === 'low')>Low</option>
                    <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                    <option value="high" @selected(request('priority') === 'high')>High</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group me-2" role="group">
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list"></i> List
                </a>
                <a href="{{ route('tasks.board') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-columns"></i> Board
                </a>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list-check text-primary"></i> Your Tasks
                <small class="text-muted ms-2">({{ $tasks->total() }} total)</small>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr class="{{ $task->isOverdue() ? 'task-overdue' : '' }}">
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                    @if ($task->isOverdue())
                                        <span class="badge badge-danger ms-2">Overdue</span>
                                    @endif
                                    @if ($task->description)
                                        <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $task->getPriorityColor() }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="badge badge-{{ $task->getStatusColor() }} border-0 dropdown-toggle"
                                                data-bs-toggle="dropdown" style="cursor: pointer;">
                                            {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                        </button>
                                        <ul class="dropdown-menu" style="background-color: white;">
                                            @foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $key => $label)
                                                @if ($task->status !== $key)
                                                    <li>
                                                        <a class="dropdown-item text-dark" href="#"
                                                           onclick="quickStatus({{ $task->id }}, '{{ $key }}'); return false;">
                                                            {{ $label }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    @if ($task->due_date)
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                        {{ $task->due_date->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $task->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-task-btn"
                                            data-task='@json($task)'
                                            data-bs-toggle="modal" data-bs-target="#editTaskModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-task-btn"
                                            data-task-id="{{ $task->id }}"
                                            data-task-title="{{ $task->title }}"
                                            data-bs-toggle="modal" data-bs-target="#deleteTaskModal">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox" style="font-size: 2rem; color: #cbd5e1;"></i>
                                    <p class="text-muted mt-3">No tasks found</p>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                        <i class="fas fa-plus"></i> Create your first task
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tasks->hasPages())
                <nav aria-label="Page navigation" class="mt-4">
                    {{ $tasks->links('pagination::bootstrap-5') }}
                </nav>
            @endif
        </div>
    </div>

    @include('tasks._form_modals')
@endsection

@section('extra-js')
    <script>
        const tasksIndexUrl = @json(route('tasks.index'));
        const tasksStoreUrl = @json(route('tasks.store'));

        document.querySelectorAll('.edit-task-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const t = JSON.parse(this.dataset.task);
                document.getElementById('editTaskId').value = t.id;
                document.getElementById('editTitle').value = t.title || '';
                document.getElementById('editDescription').value = t.description || '';
                document.getElementById('editPriority').value = t.priority || 'medium';
                document.getElementById('editStatus').value = t.status || 'pending';
                document.getElementById('editDueDate').value = t.due_date ? t.due_date.substring(0, 10) : '';
                document.querySelectorAll('#editTaskForm small.text-danger').forEach(s => s.textContent = '');
            });
        });

        function clearErrors(prefix) {
            ['Title','Description','Priority','Status','DueDate'].forEach(k => {
                const el = document.getElementById(prefix + k + 'Error');
                if (el) el.textContent = '';
            });
        }

        function applyErrors(prefix, errors) {
            const map = {
                title: 'Title', description: 'Description', priority: 'Priority',
                status: 'Status', due_date: 'DueDate'
            };
            Object.keys(errors).forEach(k => {
                const id = prefix + (map[k] || '') + 'Error';
                const el = document.getElementById(id);
                if (el) el.textContent = errors[k][0];
            });
        }

        function submitAddTask() {
            clearErrors('add');
            $.ajax({
                url: tasksStoreUrl,
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                data: {
                    title: $('#addTitle').val(),
                    description: $('#addDescription').val(),
                    priority: $('#addPriority').val(),
                    status: $('#addStatus').val(),
                    due_date: $('#addDueDate').val(),
                },
                success(res) {
                    toastr.success(res.message);
                    $('#addTaskForm')[0].reset();
                    bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
                    setTimeout(() => location.reload(), 800);
                },
                error(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        applyErrors('add', xhr.responseJSON.errors);
                        return;
                    }
                    const data = xhr.responseJSON || {};
                    if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                    else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                    else toastr.error(data.message || ('Failed to create task (HTTP ' + xhr.status + ')'));
                }
            });
        }

        function submitEditTask() {
            clearErrors('edit');
            const id = $('#editTaskId').val();
            $.ajax({
                url: `/tasks/${id}`,
                method: 'PUT',
                headers: { 'Accept': 'application/json' },
                data: {
                    title: $('#editTitle').val(),
                    description: $('#editDescription').val(),
                    priority: $('#editPriority').val(),
                    status: $('#editStatus').val(),
                    due_date: $('#editDueDate').val(),
                },
                success(res) {
                    toastr.success(res.message);
                    bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();
                    setTimeout(() => location.reload(), 800);
                },
                error(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        applyErrors('edit', xhr.responseJSON.errors);
                        return;
                    }
                    const data = xhr.responseJSON || {};
                    if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                    else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                    else toastr.error(data.message || ('Failed to update task (HTTP ' + xhr.status + ')'));
                }
            });
        }

        function quickStatus(id, status) {
            $.ajax({
                url: `/tasks/${id}/status`,
                method: 'PATCH',
                data: { status },
                success(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 500);
                },
                error() { toastr.error('Failed to update status'); }
            });
        }

        let pendingDeleteTaskId = null;

        document.querySelectorAll('.delete-task-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                pendingDeleteTaskId = this.dataset.taskId;
                document.getElementById('deleteTaskTitle').textContent = this.dataset.taskTitle || '';
            });
        });

        document.getElementById('confirmDeleteTaskBtn').addEventListener('click', function () {
            if (!pendingDeleteTaskId) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

            $.ajax({
                url: `/tasks/${pendingDeleteTaskId}`,
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                data: { _method: 'DELETE' },
                success(res) {
                    bootstrap.Modal.getInstance(document.getElementById('deleteTaskModal')).hide();
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 500);
                },
                error(xhr) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash"></i> Move to Trash';
                    const data = xhr.responseJSON || {};
                    if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                    else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                    else toastr.error(data.message || ('Error deleting task (HTTP ' + xhr.status + ')'));
                }
            });
        });

        function buildFilterUrl() {
            const params = new URLSearchParams();
            const search = $('#searchInput').val();
            const status = $('#statusFilter').val();
            const priority = $('#priorityFilter').val();
            if (search) params.set('search', search);
            if (status) params.set('status', status);
            if (priority) params.set('priority', priority);
            const qs = params.toString();
            return tasksIndexUrl + (qs ? '?' + qs : '');
        }

        let searchTimer = null;
        $('#searchInput').on('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => window.location.href = buildFilterUrl(), 400);
        });
        $('#statusFilter, #priorityFilter').on('change', function () {
            window.location.href = buildFilterUrl();
        });

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('addDueDate').setAttribute('min', today);
    </script>
@endsection
