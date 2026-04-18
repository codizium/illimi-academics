@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Subjects</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Subjects</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-subject-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Subject
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">

                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search subjects..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="subjectsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Compulsory</th>
                            <th>Teachers</th>
                            <th>Classes</th>
                            {{-- <th>Credit Units</th> --}}
                            <th>Current Syllabus</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects ?? collect() as $subject)
                            @php
                                $teacherNames = $subject->teachers
                                    ->map(
                                        fn($teacher) => $teacher->full_name ??
                                            trim(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')),
                                    )
                                    ->filter()
                                    ->values();
                                $classNames = $subject->classes->pluck('name')->filter()->values();
                                $subjectPayload = [
                                    'id' => $subject->id,
                                    'name' => $subject->name,
                                    'code' => $subject->code,
                                    'credit_units' => $subject->credit_units,
                                    'is_compulsory' => $subject->is_compulsory ? 1 : 0,
                                    'description' => $subject->description,
                                    'teacher_ids' => $subject->teachers->pluck('id')->values()->all(),
                                    'class_ids' => $subject->classes->pluck('id')->values()->all(),
                                ];
                            @endphp
                            <tr data-row-id="{{ $subject->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $subject->name }}</td>
                                <td>{{ $subject->code ?: '—' }}</td>
                                <td>
                                    @if ($subject->is_compulsory)
                                        <span
                                            class="badge text-sm fw-medium px-12 py-4 radius-8 bg-primary-50 text-primary-600">Yes</span>
                                    @else
                                        <span class="text-secondary-light">No</span>
                                    @endif
                                </td>
                                <td>{{ $teacherNames->isNotEmpty() ? $teacherNames->join(', ') : '—' }}</td>
                                <td>{{ $subject->classes->count() }}</td>
                                {{-- <td>{{ $subject->credit_units ?? '—' }}</td> --}}
                                <td>{{ $subject->currentSyllabus?->title ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary-600 js-subject-modal-trigger"
                                            data-mode="edit" data-subject='@json($subjectPayload)'>Edit</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger-600 js-subject-delete-trigger"
                                            data-subject='@json($subjectPayload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="subjectModalTitle">Add Subject</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="subjectModalSubtitle">Fill in the subject details
                            and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="subjectForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.subjects.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.subjects.update', ['subject' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Mathematics" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" placeholder="MTH" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Credit Units</label>
                            <input type="number" class="form-control" name="credit_units" placeholder="Optional" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Compulsory</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_compulsory" value="1"
                                    id="editIsCompulsory" />
                                <label class="form-check-label" for="editIsCompulsory">
                                    Required for all
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teachers</label>
                            <select class="form-select js-subject-teachers" name="teacher_ids[]" multiple>
                                @foreach ($teachers ?? collect() as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->full_name ?? trim(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Classes</label>
                            <select class="form-select js-subject-classes choice" name="class_ids[]" multiple>
                                @foreach ($classes ?? collect() as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="subjectForm" class="btn btn-primary-600" id="subjectSubmitButton">Save
                        Subject</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="subjectDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="subjectDeleteName">this
                            subject</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="subjectDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.subjects.destroy', ['subject' => '__ID__'], false) }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="subjectDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#subjectForm');
            const deleteForm = $('#subjectDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('subjectModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('subjectDeleteModal'));
            const teacherSelect = document.querySelector('#subjectForm .js-subject-teachers');
            const classSelect = document.querySelector('#subjectForm .js-subject-classes');

            const teacherChoices = teacherSelect && typeof window.Choices === 'function' ?
                new window.Choices(teacherSelect, {
                    removeItemButton: true,
                    shouldSort: false,
                    searchResultLimit: 10,
                    placeholder: true,
                    placeholderValue: 'Select teachers',
                    itemSelectText: '',
                }) :
                null;

            const classChoices = classSelect && typeof window.Choices === 'function' ?
                new window.Choices(classSelect, {
                    removeItemButton: true,
                    shouldSort: false,
                    searchResultLimit: 10,
                    placeholder: true,
                    placeholderValue: 'Select classes',
                    itemSelectText: '',
                }) :
                null;

            const setChoicesValue = (instance, values) => {
                if (!instance) {
                    return;
                }

                instance.removeActiveItems();

                (values || []).forEach((value) => {
                    if (value) {
                        instance.setChoiceByValue(String(value));
                    }
                });
            };

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#subjectModalTitle').text('Add Subject');
                $('#subjectModalSubtitle').text('Fill in the subject details and save.');
                $('#subjectSubmitButton').text('Save Subject');
                setChoicesValue(teacherChoices, []);
                setChoicesValue(classChoices, []);
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#subjectModalTitle').text(`Edit ${payload.name}`);
                $('#subjectModalSubtitle').text('Update the subject details and save your changes.');
                $('#subjectSubmitButton').text('Update Subject');
                form.find('[name="name"]').val(payload.name || '');
                form.find('[name="code"]').val(payload.code || '');
                form.find('[name="credit_units"]').val(payload.credit_units || '');
                form.find('[name="is_compulsory"]').prop('checked', payload.is_compulsory == 1);
                form.find('[name="description"]').val(payload.description || '');
                setChoicesValue(teacherChoices, payload.teacher_ids || []);
                setChoicesValue(classChoices, payload.class_ids || []);
            };

            $(document).on('click', '.js-subject-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('subject');
                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }
                modal.show();
            });

            $(document).on('click', '.js-subject-delete-trigger', function() {
                const payload = $(this).data('subject');
                deleteForm.data('deletingId', payload.id);
                $('#subjectDeleteName').text(payload.name || 'this subject');
                deleteModal.show();
            });

            $('#subjectModal').on('hidden.bs.modal', resetCreateMode);
            $('#subjectDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#subjectDeleteName').text('this subject');
            });

            bindAcademicAjaxForm({
                formSelector: '#subjectForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm
                        .data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                buildPayload: (currentForm, serializeForm) => {
                    const payload = serializeForm(currentForm);
                    payload.teacher_ids = currentForm.find('[name="teacher_ids[]"]').val() || [];
                    payload.class_ids = currentForm.find('[name="class_ids[]"]').val() || [];
                    return payload;
                },
                loadingText: 'Saving subject...',
                successTitle: 'Subject saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#subjectDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data(
                    'deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting subject...',
                successTitle: 'Subject deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
