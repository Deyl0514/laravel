@extends('layouts.app')

@section('title', 'Task Board')
@section('page-title', 'Board')

@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <p class="text-muted mb-0">Drag cards between columns to change their status.</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group me-2" role="group">
                <a href="{{ route('tasks.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-list"></i> List
                </a>
                <a href="{{ route('tasks.board') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-columns"></i> Board
                </a>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="fas fa-plus"></i> Add Task
            </button>
        </div>
    </div>

    <div class="kanban-board">
        @php
            $columnMeta = [
                'pending' => ['label' => 'Pending', 'icon' => 'fa-hourglass-half', 'color' => '#f59e0b'],
                'in_progress' => ['label' => 'In Progress', 'icon' => 'fa-spinner', 'color' => '#3b82f6'],
                'completed' => ['label' => 'Completed', 'icon' => 'fa-check-circle', 'color' => '#10b981'],
            ];
        @endphp

        @foreach ($columnMeta as $key => $meta)
            <div class="kanban-column" data-status="{{ $key }}">
                <div class="kanban-column-header">
                    <span>
                        <i class="fas {{ $meta['icon'] }}" style="color: {{ $meta['color'] }};"></i>
                        {{ $meta['label'] }}
                    </span>
                    <span class="count" id="count-{{ $key }}">{{ $columns[$key]->count() }}</span>
                </div>
                <div class="kanban-list" data-status="{{ $key }}">
                    @foreach ($columns[$key] as $task)
                        <div class="kanban-card priority-{{ $task->priority }}" data-id="{{ $task->id }}">
                            <div class="title">
                                {{ $task->title }}
                                @if ($task->isOverdue())
                                    <span class="badge badge-danger ms-1" style="font-size: 0.65rem;">Overdue</span>
                                @endif
                            </div>
                            @if ($task->description)
                                <div class="text-muted" style="font-size: 0.8rem; margin-bottom: 6px;">
                                    {{ Str::limit($task->description, 80) }}
                                </div>
                            @endif
                            <div class="meta">
                                <span class="badge badge-{{ $task->getPriorityColor() }}">{{ ucfirst($task->priority) }}</span>
                                @if ($task->due_date)
                                    <span><i class="fas fa-calendar-alt"></i> {{ $task->due_date->format('M d') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @include('tasks._form_modals')
@endsection

@section('extra-js')
    <script>
        const tasksIndexUrl = @json(route('tasks.index'));
        const tasksStoreUrl = @json(route('tasks.store'));

        document.querySelectorAll('.kanban-list').forEach(list => {
            Sortable.create(list, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    const card = evt.item;
                    const newStatus = evt.to.dataset.status;
                    const oldStatus = evt.from.dataset.status;
                    if (newStatus === oldStatus) {
                        updateCounts();
                        return;
                    }
                    const id = card.dataset.id;
                    $.ajax({
                        url: `/tasks/${id}/status`,
                        method: 'PATCH',
                        data: { status: newStatus },
                        success(res) {
                            toastr.success(res.message);
                            updateCounts();
                        },
                        error() {
                            toastr.error('Could not update status — reverting');
                            evt.from.insertBefore(card, evt.from.children[evt.oldIndex] || null);
                            updateCounts();
                        }
                    });
                }
            });
        });

        function updateCounts() {
            ['pending','in_progress','completed'].forEach(s => {
                const list = document.querySelector(`.kanban-list[data-status="${s}"]`);
                document.getElementById(`count-${s}`).textContent = list.children.length;
            });
        }

        function clearErrors(prefix) {
            ['Title','Description','Priority','Status','DueDate'].forEach(k => {
                const el = document.getElementById(prefix + k + 'Error');
                if (el) el.textContent = '';
            });
        }
        function applyErrors(prefix, errors) {
            const map = { title:'Title', description:'Description', priority:'Priority', status:'Status', due_date:'DueDate' };
            Object.keys(errors).forEach(k => {
                const el = document.getElementById(prefix + (map[k] || '') + 'Error');
                if (el) el.textContent = errors[k][0];
            });
        }
        function submitAddTask() {
            clearErrors('add');
            $.ajax({
                url: tasksStoreUrl,
                method: 'POST',
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
                    setTimeout(() => location.reload(), 600);
                },
                error(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) applyErrors('add', xhr.responseJSON.errors);
                    else toastr.error('Failed to create task');
                }
            });
        }
        function submitEditTask() {}
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('addDueDate').setAttribute('min', today);
    </script>
@endsection
