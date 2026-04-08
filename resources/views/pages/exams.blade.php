@extends('layouts.app')

@section('content')
    @php
        $subjectClassMap = ($subjects ?? collect())
            ->mapWithKeys(
                fn ($subject) => [
                    $subject->id => $subject->classes
                        ->map(fn ($class) => ['id' => $class->id, 'name' => $class->name])
                        ->values()
                        ->all(),
                ],
            )
            ->all();
    @endphp
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Exams</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Exams</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-exam-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Create Exam
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Exam Directory</h6>
                    <p class="mb-0 text-secondary-light">Manage exam setup directly from the list view.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search exams..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="examsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Starts</th>
                            <th>Ends</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($exams ?? collect()) as $exam)
                            @php
                                $payload = [
                                    'id' => $exam->id,
                                    'title' => $exam->title,
                                    'subject_id' => $exam->subject_id,
                                    'subject_name' => $exam->subject?->name,
                                    'class_id' => $exam->class_id,
                                    'class_name' => $exam->academicClass?->name,
                                    'academic_session' => $exam->academic_session,
                                    'term' => $exam->term,
                                    'duration_minutes' => $exam->duration_minutes,
                                    'total_marks' => $exam->total_marks !== null ? (float) $exam->total_marks : null,
                                    'pass_mark' => $exam->pass_mark !== null ? (float) $exam->pass_mark : null,
                                    'negative_marking' => (int) $exam->negative_marking,
                                    'negative_mark_value' => $exam->negative_mark_value !== null ? (float) $exam->negative_mark_value : null,
                                    'randomise_questions' => (int) $exam->randomise_questions,
                                    'randomise_options' => (int) $exam->randomise_options,
                                    'allow_review' => (int) $exam->allow_review,
                                    'status' => $exam->status?->value ?? $exam->status,
                                    'starts_at' => $exam?->starts_at?->format('Y-m-d\\TH:i'),
                                    'ends_at' => $exam?->ends_at?->format('Y-m-d\\TH:i'),
                                ];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $exam->title }}</td>
                                <td>{{ $exam->subject?->name ?? '—' }}</td>
                                <td>{{ $exam->academicClass?->name ?? '—' }}</td>
                                <td>{{ $exam->duration_minutes ?? '—' }}</td>
                                <td>{{ $exam->status?->label() ?? $exam->status ?? '—' }}</td>
                                <td>{{ $exam?->starts_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td>{{ $exam?->ends_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-exam-modal-trigger"
                                            data-mode="edit" data-exam='@json($payload)'>Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-exam-delete-trigger"
                                            data-exam='@json($payload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-24 text-secondary-light">No exams found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="examModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="examModalTitle">Create Exam</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="examModalSubtitle">Fill in the exam details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="examForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.exams.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.exams.update', ['id' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Subject</label>
                            <select class="form-select" name="subject_id">
                                <option value="">Select Subject</option>
                                @foreach (($subjects ?? collect()) as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select class="form-select" name="class_id">
                                <option value="">Select Subject First</option>
                            </select>
                            <div class="form-text" id="examModalClassHelp">Select a subject to load its assigned classes.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" name="duration_minutes" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Marks</label>
                            <input type="number" class="form-control" name="total_marks" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pass Mark</label>
                            <input type="number" class="form-control" name="pass_mark" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Session</label>
                            <input type="text" class="form-control" name="academic_session" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Term</label>
                            <input type="text" class="form-control" name="term" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Negative Marking</label>
                            <select class="form-select" name="negative_marking">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Negative Mark Value</label>
                            <input type="number" class="form-control" step="0.01" name="negative_mark_value" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Randomise Questions</label>
                            <select class="form-select" name="randomise_questions">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Randomise Options</label>
                            <select class="form-select" name="randomise_options">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allow Review</label>
                            <select class="form-select" name="allow_review">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Starts At</label>
                            <input type="datetime-local" class="form-control" name="starts_at" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ends At</label>
                            <input type="datetime-local" class="form-control" name="ends_at" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="examForm" class="btn btn-primary-600" id="examSubmitButton">Save Exam</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="examDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Exam</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="examDeleteName">this exam</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="examDeleteForm" data-delete-url-template="{{ route('v1.academics.exams.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="examDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#examForm');
            const deleteForm = $('#examDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('examModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('examDeleteModal'));
            const subjectSelect = form.find('[name="subject_id"]');
            const classSelect = form.find('[name="class_id"]');
            const classHelp = $('#examModalClassHelp');
            const subjectClassMap = @json($subjectClassMap);

            const rebuildClassOptions = (subjectId, selectedValue = '', selectedLabel = '') => {
                const availableClasses = subjectId ? (subjectClassMap[subjectId] || []) : [];
                const selectedId = selectedValue ? String(selectedValue) : '';
                let hasSelectedOption = false;

                classSelect.empty().append(new Option(subjectId ? 'Select Class' : 'Select Subject First', ''));

                availableClasses.forEach((item) => {
                    const isSelected = selectedId !== '' && String(item.id) === selectedId;
                    if (isSelected) {
                        hasSelectedOption = true;
                    }

                    classSelect.append(new Option(item.name, item.id, false, isSelected));
                });

                if (selectedId && !hasSelectedOption) {
                    classSelect.append(new Option(selectedLabel || 'Current class', selectedId, false, true));
                }

                if (!subjectId) {
                    classHelp.text('Select a subject to load its assigned classes.').removeClass('text-danger-600');
                    return;
                }

                if (!availableClasses.length) {
                    classHelp.text('No classes are currently assigned to this subject.').addClass('text-danger-600');
                    return;
                }

                classHelp.text('Only classes assigned to the selected subject can be used for this exam.').removeClass('text-danger-600');
            };

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#examModalTitle').text('Create Exam');
                $('#examModalSubtitle').text('Fill in the exam details and save.');
                $('#examSubmitButton').text('Save Exam');
                form.find('[name="status"]').val('scheduled');
                form.find('[name="negative_marking"]').val('0');
                form.find('[name="randomise_questions"]').val('1');
                form.find('[name="randomise_options"]').val('1');
                form.find('[name="allow_review"]').val('1');
                rebuildClassOptions('', '');
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#examModalTitle').text(`Edit ${payload.title}`);
                $('#examModalSubtitle').text('Update the exam details and save your changes.');
                $('#examSubmitButton').text('Update Exam');
                Object.entries(payload).forEach(([key, value]) => {
                    const field = form.find(`[name="${key}"]`);
                    if (field.length) {
                        field.val(value ?? '');
                    }
                });
                rebuildClassOptions(payload.subject_id || '', payload.class_id || '', payload.class_name || '');
            };

            subjectSelect.on('change', function() {
                rebuildClassOptions($(this).val(), classSelect.val());
            });

            $(document).on('click', '.js-exam-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('exam');
                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }
                modal.show();
            });

            $(document).on('click', '.js-exam-delete-trigger', function() {
                const payload = $(this).data('exam');
                deleteForm.data('deletingId', payload.id);
                $('#examDeleteName').text(payload.title || 'this exam');
                deleteModal.show();
            });

            $('#examModal').on('hidden.bs.modal', resetCreateMode);
            $('#examDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#examDeleteName').text('this exam');
            });

            rebuildClassOptions(subjectSelect.val(), classSelect.val());

            bindAcademicAjaxForm({
                formSelector: '#examForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm.data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving exam...',
                successTitle: 'Exam saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#examDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting exam...',
                successTitle: 'Exam deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
