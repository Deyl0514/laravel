@extends('layouts.app')

@section('title', 'Trash')
@section('page-title', 'Trash')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-trash-can text-danger"></i> Deleted Tasks
                <small class="text-muted ms-2">({{ $tasks->total() }})</small>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Deleted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                <td><strong>{{ $task->title }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $task->getStatusColor() }}">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $task->deleted_at->diffForHumans() }}</small></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-success restore-task-btn"
                                            data-task-id="{{ $task->id }}">
                                        <i class="fas fa-rotate-left"></i> Restore
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger force-delete-btn"
                                            data-task-id="{{ $task->id }}"
                                            data-task-title="{{ $task->title }}">
                                        <i class="fas fa-xmark"></i> Delete forever
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-trash-can" style="font-size: 2rem; color: #cbd5e1;"></i>
                                    <p class="text-muted mt-3">Trash is empty.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tasks->hasPages())
                <nav class="mt-3">{{ $tasks->links('pagination::bootstrap-5') }}</nav>
            @endif
        </div>
    </div>

    <div class="modal fade" id="forceDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-triangle-exclamation"></i> Delete forever?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">You are about to permanently delete:</p>
                    <p class="fw-bold mb-3" id="forceDeleteTitle">—</p>
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-circle-exclamation"></i>
                        This <strong>cannot be undone</strong>. The task will be removed from the database.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmForceDeleteBtn">
                        <i class="fas fa-xmark"></i> Yes, delete forever
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        window.__pendingForceDeleteId = null;
        const csrfTokenTrashed = '{{ csrf_token() }}';

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.restore-task-btn');
            if (!btn) return;
            e.preventDefault();
            doRestoreTask(btn.getAttribute('data-task-id'), btn);
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.force-delete-btn');
            if (!btn) return;
            e.preventDefault();

            window.__pendingForceDeleteId = btn.getAttribute('data-task-id');
            const titleEl = document.getElementById('forceDeleteTitle');
            if (titleEl) titleEl.textContent = btn.getAttribute('data-task-title') || '';

            const modalEl = document.getElementById('forceDeleteModal');
            if (modalEl && window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else if (confirm('Permanently delete this task? This cannot be undone.')) {
                doForceDelete();
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target.id !== 'confirmForceDeleteBtn' && !e.target.closest('#confirmForceDeleteBtn')) return;
            doForceDelete();
        });

        function doRestoreTask(id, btn) {
            if (!id) return;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';
            }
            $.ajax({
                url: '/tasks/' + id + '/restore',
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                data: { _token: csrfTokenTrashed },
                success: function (res) {
                    toastr.success(res.message || 'Task restored');
                    setTimeout(function () { location.reload(); }, 500);
                },
                error: function (xhr) {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-rotate-left"></i> Restore';
                    }
                    const data = xhr.responseJSON || {};
                    if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                    else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                    else toastr.error(data.message || ('Restore failed (HTTP ' + xhr.status + ')'));
                }
            });
        }

        function doForceDelete() {
            const id = window.__pendingForceDeleteId;
            if (!id) return;
            const confirmBtn = document.getElementById('confirmForceDeleteBtn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            }

            $.ajax({
                url: '/tasks/' + id + '/force',
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                data: { _method: 'DELETE', _token: csrfTokenTrashed },
                success: function (res) {
                    const modalEl = document.getElementById('forceDeleteModal');
                    if (modalEl && window.bootstrap) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    }
                    toastr.success(res.message || 'Task permanently deleted');
                    setTimeout(function () { location.reload(); }, 500);
                },
                error: function (xhr) {
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerHTML = '<i class="fas fa-xmark"></i> Yes, delete forever';
                    }
                    const data = xhr.responseJSON || {};
                    if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                    else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                    else toastr.error(data.message || ('Delete failed (HTTP ' + xhr.status + ')'));
                }
            });
        }
    </script>
@endsection
