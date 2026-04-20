@extends('layouts.app')

@section('title', 'Tasks Management')
@section('page-title', 'My Tasks')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="search-box">
                <input type="text" id="searchInput" class="form-control" placeholder="Search tasks...">
                <select class="form-select" id="statusFilter" style="max-width: 150px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
                <select class="form-select" id="priorityFilter" style="max-width: 150px;">
                    <option value="">All Priority</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
                <button class="btn btn-primary" onclick="filterTasks()">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list-check text-primary"></i> Your Tasks
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
                                    <span class="badge badge-{{ $task->getStatusColor() }}">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
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
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="editTask({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ addslashes($task->description) }}', '{{ $task->priority }}', '{{ $task->status }}', '{{ $task->due_date }}')"
                                            data-bs-toggle="modal" data-bs-target="#editTaskModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="deleteTask({{ $task->id }}, '{{ $task->title }}')">
                                        <i class="fas fa-trash"></i> Delete
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

            <!-- Pagination -->
            @if ($tasks->hasPages())
                <nav aria-label="Page navigation" class="mt-4">
                    {{ $tasks->links('pagination::bootstrap-5') }}
                </nav>
            @endif
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i> Create New Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addTaskForm">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="addTitle" required>
                            <small class="text-danger" id="addTitleError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="addDescription" rows="3"></textarea>
                            <small class="text-danger" id="addDescriptionError"></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" id="addPriority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <small class="text-danger" id="addPriorityError"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="addStatus" required>
                                    <option value="">Select Status</option>
                                    <option value="pending" selected>Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <small class="text-danger" id="addStatusError"></small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="addDueDate">
                            <small class="text-danger" id="addDueDateError"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitAddTask()">
                            <i class="fas fa-save"></i> Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Edit Task
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editTaskForm">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editTaskId">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" required>
                            <small class="text-danger" id="editTitleError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="editDescription" rows="3"></textarea>
                            <small class="text-danger" id="editDescriptionError"></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-select" id="editPriority" required>
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                <small class="text-danger" id="editPriorityError"></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="editStatus" required>
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <small class="text-danger" id="editStatusError"></small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="editDueDate">
                            <small class="text-danger" id="editDueDateError"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitEditTask()">
                            <i class="fas fa-save"></i> Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        function editTask(id, title, description, priority, status, dueDate) {
            document.getElementById('editTaskId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editPriority').value = priority;
            document.getElementById('editStatus').value = status;
            document.getElementById('editDueDate').value = dueDate;
        }

        function submitAddTask() {
            const title = document.getElementById('addTitle').value;
            const description = document.getElementById('addDescription').value;
            const priority = document.getElementById('addPriority').value;
            const status = document.getElementById('addStatus').value;
            const dueDate = document.getElementById('addDueDate').value;

            $.ajax({
                url: '{{ route("tasks.store") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    title: title,
                    description: description,
                    priority: priority,
                    status: status,
                    due_date: dueDate
                },
                success: function(response) {
                    toastr.success(response.message);
                    document.getElementById('addTaskForm').reset();
                    bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.title) {
                            document.getElementById('addTitleError').textContent = xhr.responseJSON.errors.title[0];
                        }
                        if (xhr.responseJSON.errors.priority) {
                            document.getElementById('addPriorityError').textContent = xhr.responseJSON.errors.priority[0];
                        }
                        if (xhr.responseJSON.errors.status) {
                            document.getElementById('addStatusError').textContent = xhr.responseJSON.errors.status[0];
                        }
                        if (xhr.responseJSON.errors.due_date) {
                            document.getElementById('addDueDateError').textContent = xhr.responseJSON.errors.due_date[0];
                        }
                    }
                }
            });
        }

        function submitEditTask() {
            const id = document.getElementById('editTaskId').value;
            const title = document.getElementById('editTitle').value;
            const description = document.getElementById('editDescription').value;
            const priority = document.getElementById('editPriority').value;
            const status = document.getElementById('editStatus').value;
            const dueDate = document.getElementById('editDueDate').value;

            $.ajax({
                url: `/tasks/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    title: title,
                    description: description,
                    priority: priority,
                    status: status,
                    due_date: dueDate
                },
                success: function(response) {
                    toastr.success(response.message);
                    bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON.errors) {
                        if (xhr.responseJSON.errors.title) {
                            document.getElementById('editTitleError').textContent = xhr.responseJSON.errors.title[0];
                        }
                        if (xhr.responseJSON.errors.priority) {
                            document.getElementById('editPriorityError').textContent = xhr.responseJSON.errors.priority[0];
                        }
                        if (xhr.responseJSON.errors.status) {
                            document.getElementById('editStatusError').textContent = xhr.responseJSON.errors.status[0];
                        }
                        if (xhr.responseJSON.errors.due_date) {
                            document.getElementById('editDueDateError').textContent = xhr.responseJSON.errors.due_date[0];
                        }
                    }
                }
            });
        }

        function deleteTask(id, title) {
            if (confirm(`Are you sure you want to delete "${title}"?`)) {
                $.ajax({
                    url: `/tasks/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function() {
                        toastr.error('Error deleting task');
                    }
                });
            }
        }

        function filterTasks() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const priority = document.getElementById('priorityFilter').value;
            let url = '{{ route("tasks.index") }}?';
            if (search) url += 'search=' + encodeURIComponent(search) + '&';
            if (status) url += 'status=' + status + '&';
            if (priority) url += 'priority=' + priority;
            window.location.href = url;
        }

        // Allow Enter key to trigger filter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                filterTasks();
            }
        });

        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('addDueDate').setAttribute('min', today);
        document.getElementById('editDueDate').setAttribute('min', today);
    </script>
@endsection
