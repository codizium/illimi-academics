@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Classrooms</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Classrooms</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-classroom-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Classroom
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search classrooms..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="classroomsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Capacity</th>
                            <th>Location</th>
                            <th>Classes</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classrooms ?? collect() as $classroom)
                            @php
                                $classroomPayload = [
                                    'id' => $classroom->id,
                                    'name' => $classroom->name,
                                    'code' => $classroom->code,
                                    'capacity' => $classroom->capacity,
                                    'location' => $classroom->location,
                                    'description' => $classroom->description,
                                ];
                            @endphp
                            <tr data-row-id="{{ $classroom->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $classroom->name }}</td>
                                <td>{{ $classroom->code ?: '—' }}</td>
                                <td>{{ $classroom->capacity ?? '—' }}</td>
                                <td>{{ $classroom->location ?: '—' }}</td>
                                <td>{{ $classroom->academic_classes_count ?? 0 }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary-600 js-classroom-modal-trigger"
                                            data-mode="edit" data-classroom='@json($classroomPayload)'>Edit</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger-600 js-classroom-delete-trigger"
                                            data-classroom='@json($classroomPayload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="classroomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="classroomModalTitle">Add Classroom</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="classroomModalSubtitle">Fill in the classroom
                            details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="classroomForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.classrooms.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.classrooms.update', ['classroom' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Science Lab 1" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" placeholder="LAB-01" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" placeholder="40" />
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location"
                                placeholder="North Wing, First Floor" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4"
                                placeholder="Optional notes about facilities or usage."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="classroomForm" class="btn btn-primary-600" id="classroomSubmitButton">Save
                        Classroom</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="classroomDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Classroom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="classroomDeleteName">this
                            classroom</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="classroomDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.classrooms.destroy', ['classroom' => '__ID__'], false) }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="classroomDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#classroomForm');
            const deleteForm = $('#classroomDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('classroomModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('classroomDeleteModal'));

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#classroomModalTitle').text('Add Classroom');
                $('#classroomModalSubtitle').text('Fill in the classroom details and save.');
                $('#classroomSubmitButton').text('Save Classroom');
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#classroomModalTitle').text(`Edit ${payload.name}`);
                $('#classroomModalSubtitle').text('Update the classroom details and save your changes.');
                $('#classroomSubmitButton').text('Update Classroom');
                form.find('[name="name"]').val(payload.name || '');
                form.find('[name="code"]').val(payload.code || '');
                form.find('[name="capacity"]').val(payload.capacity || '');
                form.find('[name="location"]').val(payload.location || '');
                form.find('[name="description"]').val(payload.description || '');
            };

            $(document).on('click', '.js-classroom-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('classroom');

                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }

                modal.show();
            });

            $(document).on('click', '.js-classroom-delete-trigger', function() {
                const payload = $(this).data('classroom');
                deleteForm.data('deletingId', payload.id);
                $('#classroomDeleteName').text(payload.name || 'this classroom');
                deleteModal.show();
            });

            $('#classroomModal').on('hidden.bs.modal', resetCreateMode);
            $('#classroomDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#classroomDeleteName').text('this classroom');
            });

            bindAcademicAjaxForm({
                formSelector: '#classroomForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm
                        .data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving classroom...',
                successTitle: 'Classroom saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#classroomDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data(
                    'deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting classroom...',
                successTitle: 'Classroom deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
