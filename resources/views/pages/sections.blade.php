@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div class="">
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Sections</h1>
            <div class="">
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/sections" class="text-secondary-light hover-text-primary hover-underline">/ Sections</a>
                <span class="text-secondary-light">/ List</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-section-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
            Add Section
        </button>
    </div>

    <div class="card">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center justify-content-between">
            <h6 class="text-lg fw-semibold mb-0">All Sections</h6>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive">
                <table class="table" id="sectionsTable">
                    <thead>
                        <tr>
                            <th class="text-sm fw-semibold text-primary-light">Name</th>
                            <th class="text-sm fw-semibold text-primary-light">Description</th>
                            <th class="text-sm fw-semibold text-primary-light">Classes</th>
                            <th class="text-sm fw-semibold text-primary-light">Created</th>
                            <th class="text-sm fw-semibold text-primary-light">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sectionsTableBody">
                        @forelse($sections as $section)
                            @php
                                $sectionPayload = [
                                    'id' => $section->id,
                                    'name' => $section->name,
                                    'description' => $section->description,
                                ];
                            @endphp
                            <tr data-section-id="{{ $section->id }}">
                                <td class="section-name">{{ $section->name }}</td>
                                <td class="section-description">{{ $section->description ?? '—' }}</td>
                                <td class="section-classes-count">
                                    {{ $section->classes_count ?? $section->classes->count() }}</td>
                                <td class="section-created">{{ $section->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary-600 js-section-modal-trigger"
                                            data-mode="edit" data-section='{{ json_encode($sectionPayload) }}'>
                                            <i class="ri-edit-line"></i> Edit
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger-600 js-section-delete-trigger"
                                            data-id="{{ $section->id }}" data-name="{{ $section->name }}">
                                            <i class="ri-delete-bin-line"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="sectionsEmptyRow">
                                <td colspan="5" class="text-center py-24 text-secondary-light">
                                    No sections found. Click "Add Section" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Section Modal -->
    <div class="modal fade" id="sectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="sectionModalTitle">Add Section</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="sectionModalSubtitle">Fill in the section details
                            and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="sectionForm" class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" placeholder="Science" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Section description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="sectionForm" class="btn btn-primary-600" id="sectionSubmitButton">Save
                        Section</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Section Modal -->
    <div class="modal fade" id="sectionDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="sectionDeleteName">this section</strong>?
                    </p>
                    <p class="text-sm text-secondary-light">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger-600" id="sectionDeleteConfirm">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const form = $('#sectionForm');
            const deleteBtn = $('#sectionDeleteConfirm');
            const modal = new bootstrap.Modal(document.getElementById('sectionModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('sectionDeleteModal'));
            const tableBody = $('#sectionsTableBody');

            function resetCreateMode() {
                form[0].reset();
                form.attr('action', '{{ route('v1.academics.sections.store') }}');
                form.data('method', 'POST');
                form.data('editingId', '');
            }

            function openEditMode(payload) {
                form.find('[name="name"]').val(payload.name || '');
                form.find('[name="description"]').val(payload.description || '');
                form.attr('action', `{{ route('v1.academics.sections.store') }}/${payload.id}`);
                form.data('method', 'PUT');
                form.data('editingId', payload.id);
                $('#sectionModalTitle').text(`Edit ${payload.name}`);
                $('#sectionModalSubtitle').text('Update the section details and save your changes.');
                $('#sectionSubmitButton').text('Update Section');
            }

            function renderSectionRow(section) {
                const sectionPayload = {
                    id: section.id,
                    name: section.name,
                    description: section.description,
                };
                return `
                    <tr data-section-id="${section.id}">
                        <td class="section-name">${section.name || ''}</td>
                        <td class="section-description">${section.description || '—'}</td>
                        <td class="section-classes-count">${section.classes_count || 0}</td>
                        <td class="section-created">${section.created_at ? new Date(section.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary-600 js-section-modal-trigger" data-mode="edit" data-section='${JSON.stringify(sectionPayload).replace(/'/g, "'")}'>
                                    <i class="ri-edit-line"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger-600 js-section-delete-trigger" data-id="${section.id}" data-name="${section.name}">
                                    <i class="ri-delete-bin-line"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }

            function addSectionRow(section) {
                $('#sectionsEmptyRow').remove();
                const existingRow = $(`tr[data-section-id="${section.id}"]`);
                if (existingRow.length) {
                    existingRow.replaceWith(renderSectionRow(section));
                } else {
                    tableBody.append(renderSectionRow(section));
                }
            }

            function updateSectionRow(section) {
                const existingRow = $(`tr[data-section-id="${section.id}"]`);
                if (existingRow.length) {
                    existingRow.replaceWith(renderSectionRow(section));
                } else {
                    addSectionRow(section);
                }
            }

            function removeSectionRow(sectionId) {
                const existingRow = $(`tr[data-section-id="${sectionId}"]`);
                if (existingRow.length) {
                    existingRow.remove();
                }
            }

            $(document).on('click', '.js-section-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('section');

                if (mode === 'create') {
                    resetCreateMode();
                } else if (mode === 'edit' && payload) {
                    openEditMode(payload);
                }
                modal.show();
            });

            $(document).on('click', '.js-section-delete-trigger', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#sectionDeleteName').text(name || 'this section');
                deleteBtn.data('deletingId', id);
                deleteModal.show();
            });

            $('#sectionModal').on('hidden.bs.modal', resetCreateMode);

            form.on('submit', function(e) {
                e.preventDefault();

                const payload = {};
                form.serializeArray().forEach(item => {
                    payload[item.name] = item.value;
                });

                const editingId = form.data('editingId');
                const isEdit = !!editingId;
                const url = isEdit ?
                    `{{ route('v1.academics.sections.store') }}/${editingId}` :
                    '{{ route('v1.academics.sections.store') }}';
                const method = isEdit ? 'PUT' : 'POST';

                $.ajax({
                    url,
                    method,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: payload,
                    beforeSend: function() {
                        $('#sectionSubmitButton').prop('disabled', true).text('Saving...');
                    },
                    success: function(response) {
                        const section = response.data;
                        Swal.fire({
                            icon: 'success',
                            title: isEdit ? 'Section updated' : 'Section created',
                            text: response.message || 'Section saved successfully'
                        }).then(() => {
                            modal.hide();
                            // Update table dynamically instead of reloading
                            if (isEdit) {
                                updateSectionRow(section);
                            } else {
                                addSectionRow(section);
                            }
                        });
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save section'
                        });
                    },
                    complete: function() {
                        $('#sectionSubmitButton').prop('disabled', false).text(isEdit ?
                            'Update Section' : 'Save Section');
                    }
                });
            });

            deleteBtn.on('click', function() {
                const id = $(deleteBtn).data('deletingId');

                $.ajax({
                    url: `{{ route('v1.academics.sections.store') }}/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        deleteBtn.prop('disabled', true).text('Deleting...');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Section deleted',
                            text: response.message || 'Section deleted successfully'
                        }).then(() => {
                            deleteModal.hide();
                            removeSectionRow(id);
                        });
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to delete section'
                        });
                    },
                    complete: function() {
                        deleteBtn.prop('disabled', false).text('Delete');
                    }
                });
            });

        })();
    </script>
@endpush
