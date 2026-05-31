@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
    <div class="row mb-3">
        <div class="col-md-8">
            <p class="text-muted mb-0">Organize tasks by color-coded categories. Categories are private to your account.</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tags text-primary"></i> Your Categories</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Tasks</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $cat)
                            <tr>
                                <td>
                                    <span class="category-chip" style="background-color: {{ $cat->color }}1a; color: {{ $cat->color }};">
                                        <span class="dot"></span>{{ $cat->name }}
                                    </span>
                                </td>
                                <td>
                                    <code>{{ $cat->color }}</code>
                                </td>
                                <td>{{ $cat->tasks_count }}</td>
                                <td><small class="text-muted">{{ $cat->created_at->diffForHumans() }}</small></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary edit-cat-btn"
                                            data-cat='@json($cat)'
                                            data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                            onclick="deleteCategory({{ $cat->id }}, @json($cat->name))">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-tags" style="font-size: 2rem; color: #cbd5e1;"></i>
                                    <p class="text-muted mt-3">No categories yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($categories->hasPages())
                <nav class="mt-3">{{ $categories->links('pagination::bootstrap-5') }}</nav>
            @endif
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCatForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="addCatName" maxlength="60" required>
                            <small class="text-danger" id="addCatNameError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="addCatColor" value="#3b82f6">
                            <small class="text-danger" id="addCatColorError"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitAddCat()">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCatForm">
                    <div class="modal-body">
                        <input type="hidden" id="editCatId">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="editCatName" maxlength="60" required>
                            <small class="text-danger" id="editCatNameError"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="editCatColor">
                            <small class="text-danger" id="editCatColorError"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitEditCat()">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        document.querySelectorAll('.edit-cat-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const c = JSON.parse(this.dataset.cat);
                document.getElementById('editCatId').value = c.id;
                document.getElementById('editCatName').value = c.name;
                document.getElementById('editCatColor').value = c.color;
                document.getElementById('editCatNameError').textContent = '';
                document.getElementById('editCatColorError').textContent = '';
            });
        });

        function showErrors(prefix, errors) {
            ['name','color'].forEach(k => {
                const el = document.getElementById(prefix + (k === 'name' ? 'Name' : 'Color') + 'Error');
                if (el) el.textContent = errors[k] ? errors[k][0] : '';
            });
        }

        function submitAddCat() {
            $.ajax({
                url: @json(route('categories.store')),
                method: 'POST',
                data: {
                    name: $('#addCatName').val(),
                    color: $('#addCatColor').val(),
                },
                success(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 500);
                },
                error(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) showErrors('addCat', xhr.responseJSON.errors);
                    else toastr.error('Failed to create category');
                }
            });
        }

        function submitEditCat() {
            const id = $('#editCatId').val();
            $.ajax({
                url: `/categories/${id}`,
                method: 'PUT',
                data: {
                    name: $('#editCatName').val(),
                    color: $('#editCatColor').val(),
                },
                success(res) {
                    toastr.success(res.message);
                    setTimeout(() => location.reload(), 500);
                },
                error(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) showErrors('editCat', xhr.responseJSON.errors);
                    else toastr.error('Failed to update category');
                }
            });
        }

        function deleteCategory(id, name) {
            confirmDelete(`Delete category "${name}"?`, 'Tasks in this category will keep their tasks but lose the category tag.').then(result => {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: `/categories/${id}`,
                    method: 'DELETE',
                    success(res) {
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 500);
                    },
                    error() { toastr.error('Failed to delete category'); }
                });
            });
        }
    </script>
@endsection
