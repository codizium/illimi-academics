@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Classes</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Classes</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-class-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Class
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Class Directory</h6>
                    <p class="mb-0 text-secondary-light">Create, update, and remove academic classes in place.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search classes..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="classesTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Level</th>
                            <th>Section</th>
                            <th>Classroom</th>
                            <th>Capacity</th>
                            <th>Teacher</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (($classes ?? collect()) as $class)
                            @php
                                $classPayload = [
                                    'id' => $class->id,
                                    'name' => $class->name,
                                    'level' => $class->level,
                                    'section_id' => $class->section_id,
                                    'classroom_id' => $class->classroom_id,
                                    'capacity' => $class->capacity,
                                    'class_teacher_id' => $class->class_teacher_id,
                                ];
                            @endphp
                            <tr data-row-id="{{ $class->id }}">
                                <td class="row-index">{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $class->name }}</td>
                                <td>{{ $class->level ?: '—' }}</td>
                                <td>{{ $class->section?->name ?: '—' }}</td>
                                <td>{{ $class->classroom?->name ?: '—' }}</td>
                                <td>{{ $class->capacity ?? '—' }}</td>
                                <td>{{ $class->classTeacher?->full_name ?? trim(($class->classTeacher?->first_name ?? '') . ' ' . ($class->classTeacher?->last_name ?? '')) ?: '—' }}
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary-600 js-class-modal-trigger"
                                            data-mode="edit" data-classroom='@json($classPayload)'>Edit</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger-600 js-class-delete-trigger"
                                            data-classroom='@json($classPayload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="classModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="classModalTitle">Add Class</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="classModalSubtitle">Fill in the class details and
                            save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="classForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.classes.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.classes.update', ['id' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="JSS 1" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Level</label>
                            <input type="text" class="form-control" name="level" placeholder="Junior" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <select class="form-select" name="section_id">
                                <option value="">Select Section</option>
                                @foreach ($sections ?? collect() as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Classroom</label>
                            <select class="form-select" name="classroom_id">
                                <option value="">Select Classroom</option>
                                @foreach ($classrooms ?? collect() as $classroom)
                                    <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" placeholder="40" />
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Class Teacher</label>
                            <select class="form-select" name="class_teacher_id">
                                <option value="">Select Staff</option>
                                @foreach ($teachers ?? collect() as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->full_name ?? trim(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="classForm" class="btn btn-primary-600" id="classSubmitButton">Save
                        Class</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="classDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="classDeleteName">this class</strong>?
                    </p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="classDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.classes.destroy', ['id' => '__ID__'], false) }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="classDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#classForm');
            const deleteForm = $('#classDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('classModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('classDeleteModal'));

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#classModalTitle').text('Add Class');
                $('#classModalSubtitle').text('Fill in the class details and save.');
                $('#classSubmitButton').text('Save Class');
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#classModalTitle').text(`Edit ${payload.name}`);
                $('#classModalSubtitle').text('Update the class details and save your changes.');
                $('#classSubmitButton').text('Update Class');
                form.find('[name="name"]').val(payload.name || '');
                form.find('[name="level"]').val(payload.level || '');
                form.find('[name="section_id"]').val(payload.section_id || '');
                form.find('[name="classroom_id"]').val(payload.classroom_id || '');
                form.find('[name="capacity"]').val(payload.capacity || '');
                form.find('[name="class_teacher_id"]').val(payload.class_teacher_id || '');
            };

            $(document).on('click', '.js-class-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('classroom');
                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }
                modal.show();
            });

            $(document).on('click', '.js-class-delete-trigger', function() {
                const payload = $(this).data('classroom');
                deleteForm.data('deletingId', payload.id);
                $('#classDeleteName').text(payload.name || 'this class');
                deleteModal.show();
            });

            $('#classModal').on('hidden.bs.modal', resetCreateMode);
            $('#classDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#classDeleteName').text('this class');
            });

            bindAcademicAjaxForm({
                formSelector: '#classForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm
                        .data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving class...',
                successTitle: 'Class saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#classDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data(
                    'deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting class...',
                successTitle: 'Class deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
