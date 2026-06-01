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
                                    <button class="btn btn-sm btn-success" onclick="restoreTask({{ $task->id }})">
                                        <i class="fas fa-rotate-left"></i> Restore
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="forceDelete({{ $task->id }}, @json($task->title))">
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
@endsection

@section('extra-js')
    <script>
        function restoreTask(id) {
            $.ajax({
                url: `/tasks/${id}/restore`,
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                success(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 500);
                },
                error(xhr) {
                    const data = xhr.responseJSON || {};
                    if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                    else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                    else toastr.error(data.message || ('Restore failed (HTTP ' + xhr.status + ')'));
                }
            });
        }

        function forceDelete(id, title) {
            Swal.fire({
                title: `Permanently delete "${title}"?`,
                text: 'This cannot be undone.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete forever',
            }).then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/tasks/${id}/force`,
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    data: { _method: 'DELETE' },
                    success(res) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 500);
                    },
                    error(xhr) {
                        const data = xhr.responseJSON || {};
                        if (xhr.status === 419) toastr.error('Session expired (419). Refresh the page.');
                        else if (xhr.status === 0) toastr.error('Network error — server unreachable.');
                        else toastr.error(data.message || ('Delete failed (HTTP ' + xhr.status + ')'));
                    }
                });
            });
        }
    </script>
@endsection
