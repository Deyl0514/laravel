@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card tasks">
                <div class="stat-icon"><i class="fas fa-list-check"></i></div>
                <div class="stat-number">{{ $totalTasks }}</div>
                <div class="stat-label">My Tasks</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card completed">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number">{{ $completedTasks }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-number">{{ $pendingTasks }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card" style="border-left-color: var(--danger-accent);">
                <div class="stat-icon" style="color: var(--danger-accent);"><i class="fas fa-triangle-exclamation"></i></div>
                <div class="stat-number">{{ $overdueTasks }}</div>
                <div class="stat-label">Overdue</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong><i class="fas fa-bullseye text-primary"></i> Completion Rate</strong>
                <span class="text-muted">{{ $completedTasks }} / {{ $totalTasks }} ({{ $completionRate }}%)</span>
            </div>
            <div class="progress-thin">
                <div class="bar" style="width: {{ $completionRate }}%;"></div>
            </div>
            <small class="text-muted d-block mt-2">
                <i class="fas fa-users"></i> {{ $totalUsers }} registered user{{ $totalUsers === 1 ? '' : 's' }}
            </small>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary"></i> Tasks by Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-warning"></i> Tasks by Priority
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-info"></i> Recent Tasks
                    </h5>
                </div>
                <div class="card-body">
                    @if ($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentTasks as $task)
                                        <tr class="{{ $task->isOverdue() ? 'task-overdue' : '' }}">
                                            <td>
                                                <strong>{{ $task->title }}</strong>
                                                @if ($task->isOverdue())
                                                    <span class="badge badge-danger ms-2">Overdue</span>
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
                                                <small class="text-muted">
                                                    {{ $task->created_at->diffForHumans() }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                            <p class="text-muted mt-3">No tasks yet. Start by creating your first task!</p>
                            <a href="{{ route('tasks.index') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Create Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    label: 'Tasks',
                    data: [
                        {{ $tasksByStatus['pending'] }},
                        {{ $tasksByStatus['in_progress'] }},
                        {{ $tasksByStatus['completed'] }}
                    ],
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)'
                    ],
                    borderColor: [
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        const priorityChart = new Chart(priorityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Low', 'Medium', 'High'],
                datasets: [{
                    data: [
                        {{ $tasksByPriority['low'] }},
                        {{ $tasksByPriority['medium'] }},
                        {{ $tasksByPriority['high'] }}
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
