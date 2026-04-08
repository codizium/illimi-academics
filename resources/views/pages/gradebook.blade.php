@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Gradebook</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Gradebook</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-gradebook-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Entry
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Gradebook Entries</h6>
                    <p class="mb-0 text-secondary-light">Capture scores and keep the list in sync without refreshing.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search gradebook..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="gradebookTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Session / Term</th>
                            <th>Component</th>
                            <th>Score</th>
                            <th>Weight</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($entries ?? collect()) as $entry)
                            @php
                                $entryPayload = [
                                    'id' => $entry->id,
                                    'student_id' => $entry->student_id,
                                    'subject_id' => $entry->subject_id,
                                    'class_id' => $entry->class_id,
                                    'academic_session' => $entry->academic_session,
                                    'term' => $entry->term,
                                    'component' => $entry->component?->value ?? $entry->component,
                                    'score' => $entry->score !== null ? (float) $entry->score : null,
                                    'max_score' => $entry->max_score !== null ? (float) $entry->max_score : null,
                                    'weight' => $entry->weight !== null ? (float) $entry->weight : null,
                                    'student_name' => $entry->student?->full_name ?? trim(($entry->student?->first_name ?? '').' '.($entry->student?->last_name ?? '')),
                                ];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $entry->student?->full_name ?? trim(($entry->student?->first_name ?? '').' '.($entry->student?->last_name ?? '')) ?: '—' }}</td>
                                <td>{{ $entry->subject?->name ?? '—' }}</td>
                                <td>{{ $entry->academicClass?->name ?? '—' }}</td>
                                <td>
                                    <div>{{ $entry->academic_session ?: '—' }}</div>
                                    <div class="text-sm text-secondary-light">{{ $entry->term ?: '—' }}</div>
                                </td>
                                <td>{{ $entry->component?->label() ?? $entry->component ?? '—' }}</td>
                                <td>{{ $entry->score ?? '—' }} / {{ $entry->max_score ?? '—' }}</td>
                                <td>{{ $entry->weight ?? '—' }}%</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-gradebook-modal-trigger"
                                            data-mode="edit" data-entry='@json($entryPayload)'>Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-gradebook-delete-trigger"
                                            data-entry='@json($entryPayload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-24 text-secondary-light">No gradebook entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gradebookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="gradebookModalTitle">Add Gradebook Entry</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="gradebookModalSubtitle">Fill in the entry details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="gradebookForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.gradebook.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.gradebook.update', ['id' => '__ID__'], false) }}">
                        <div class="col-md-4">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id">
                                <option value="">Select Student</option>
                                @foreach (($students ?? collect()) as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name ?? trim(($student->first_name ?? '').' '.($student->last_name ?? '')) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Subject</label>
                            <select class="form-select" name="subject_id">
                                <option value="">Select Subject</option>
                                @foreach (($subjects ?? collect()) as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Class</label>
                            <select class="form-select" name="class_id">
                                <option value="">Select Class</option>
                                @foreach (($classes ?? collect()) as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Component</label>
                            <select class="form-select" name="component">
                                @foreach (($components ?? []) as $component)
                                    <option value="{{ $component->value }}">{{ $component->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Score</label>
                            <input type="number" class="form-control" name="score" step="0.01" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Max Score</label>
                            <input type="number" class="form-control" name="max_score" step="0.01" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Weight (%)</label>
                            <input type="number" class="form-control" name="weight" step="0.01" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Session</label>
                            <input type="text" class="form-control" name="academic_session" placeholder="2025/2026" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Term</label>
                            <input type="text" class="form-control" name="term" placeholder="First Term" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="gradebookForm" class="btn btn-primary-600" id="gradebookSubmitButton">Save Entry</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gradebookDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Gradebook Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="gradebookDeleteName">this entry</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="gradebookDeleteForm" data-delete-url-template="{{ route('v1.academics.gradebook.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="gradebookDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#gradebookForm');
            const deleteForm = $('#gradebookDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('gradebookModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('gradebookDeleteModal'));

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#gradebookModalTitle').text('Add Gradebook Entry');
                $('#gradebookModalSubtitle').text('Fill in the entry details and save.');
                $('#gradebookSubmitButton').text('Save Entry');
                const firstComponent = form.find('[name="component"] option:first').val();
                if (firstComponent) {
                    form.find('[name="component"]').val(firstComponent);
                }
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#gradebookModalTitle').text(`Edit ${payload.student_name || 'Gradebook Entry'}`);
                $('#gradebookModalSubtitle').text('Update the gradebook entry and save your changes.');
                $('#gradebookSubmitButton').text('Update Entry');
                form.find('[name="student_id"]').val(payload.student_id || '');
                form.find('[name="subject_id"]').val(payload.subject_id || '');
                form.find('[name="class_id"]').val(payload.class_id || '');
                form.find('[name="academic_session"]').val(payload.academic_session || '');
                form.find('[name="term"]').val(payload.term || '');
                form.find('[name="component"]').val(payload.component || '');
                form.find('[name="score"]').val(payload.score || '');
                form.find('[name="max_score"]').val(payload.max_score || '');
                form.find('[name="weight"]').val(payload.weight || '');
            };

            $(document).on('click', '.js-gradebook-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('entry');
                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }
                modal.show();
            });

            $(document).on('click', '.js-gradebook-delete-trigger', function() {
                const payload = $(this).data('entry');
                deleteForm.data('deletingId', payload.id);
                $('#gradebookDeleteName').text(payload.student_name || 'this entry');
                deleteModal.show();
            });

            $('#gradebookModal').on('hidden.bs.modal', resetCreateMode);
            $('#gradebookDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#gradebookDeleteName').text('this entry');
            });

            bindAcademicAjaxForm({
                formSelector: '#gradebookForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm.data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving gradebook entry...',
                successTitle: 'Gradebook entry saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#gradebookDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting gradebook entry...',
                successTitle: 'Gradebook entry deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
