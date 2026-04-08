@extends('layouts.app')

@section('content')
    @php
        $allClasses = ($classes ?? collect())
            ->map(fn ($class) => ['id' => $class->id, 'name' => $class->name])
            ->values();
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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Question Banks</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Question Banks</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-question-bank-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Bank
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Question Bank Directory</h6>
                    <p class="mb-0 text-secondary-light">Manage question banks with live updates.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search question banks..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="questionBanksTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Questions</th>
                            <th>Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($questionBanks ?? collect()) as $questionBank)
                            @php
                                $payload = [
                                    'id' => $questionBank->id,
                                    'name' => $questionBank->name,
                                    'subject_id' => $questionBank->subject_id,
                                    'subject_name' => $questionBank->subject?->name,
                                    'class_id' => $questionBank->class_id,
                                    'class_name' => $questionBank->academicClass?->name,
                                    'description' => $questionBank->description,
                                ];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $questionBank->name }}</td>
                                <td>{{ $questionBank->subject?->name ?? '—' }}</td>
                                <td>{{ $questionBank->academicClass?->name ?? '—' }}</td>
                                <td>{{ $questionBank->questions_count ?? 0 }}</td>
                                <td>{{ $questionBank->description ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-question-bank-modal-trigger"
                                            data-mode="edit" data-question-bank='@json($payload)'>Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-question-bank-delete-trigger"
                                            data-question-bank='@json($payload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-24 text-secondary-light">No question banks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="questionBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="questionBankModalTitle">Add Question Bank</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="questionBankModalSubtitle">Fill in the question bank details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="questionBankForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.question_banks.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.question_banks.update', ['id' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <select class="form-select" name="subject_id">
                                <option value="">Select Subject</option>
                                @foreach (($subjects ?? collect()) as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Class</label>
                            <select class="form-select" name="class_id">
                                <option value="">Select Class</option>
                                @foreach (($classes ?? collect()) as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text" id="questionBankModalClassHelp">Choose a subject to narrow the class list.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" name="description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="questionBankForm" class="btn btn-primary-600" id="questionBankSubmitButton">Save Bank</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="questionBankDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Question Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="questionBankDeleteName">this question bank</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="questionBankDeleteForm" data-delete-url-template="{{ route('v1.academics.question_banks.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="questionBankDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#questionBankForm');
            const deleteForm = $('#questionBankDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('questionBankModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('questionBankDeleteModal'));
            const subjectSelect = form.find('[name="subject_id"]');
            const classSelect = form.find('[name="class_id"]');
            const classHelp = $('#questionBankModalClassHelp');
            const allClasses = @json($allClasses);
            const subjectClassMap = @json($subjectClassMap);

            const rebuildClassOptions = (subjectId, selectedValue = '', selectedLabel = '') => {
                const availableClasses = subjectId ? (subjectClassMap[subjectId] || []) : allClasses;
                const selectedId = selectedValue ? String(selectedValue) : '';
                let hasSelectedOption = false;

                classSelect.empty().append(new Option('Select Class', ''));

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
                    classHelp.text('Choose a subject to narrow the class list.').removeClass('text-danger-600');
                    return;
                }

                if (!availableClasses.length) {
                    classHelp.text('No classes are currently assigned to this subject.').addClass('text-danger-600');
                    return;
                }

                classHelp.text('Only classes assigned to the selected subject are shown.').removeClass('text-danger-600');
            };

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#questionBankModalTitle').text('Add Question Bank');
                $('#questionBankModalSubtitle').text('Fill in the question bank details and save.');
                $('#questionBankSubmitButton').text('Save Bank');
                rebuildClassOptions('', '');
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#questionBankModalTitle').text(`Edit ${payload.name}`);
                $('#questionBankModalSubtitle').text('Update the question bank details and save your changes.');
                $('#questionBankSubmitButton').text('Update Bank');
                form.find('[name="name"]').val(payload.name || '');
                form.find('[name="subject_id"]').val(payload.subject_id || '');
                rebuildClassOptions(payload.subject_id || '', payload.class_id || '', payload.class_name || '');
                form.find('[name="description"]').val(payload.description || '');
            };

            subjectSelect.on('change', function() {
                rebuildClassOptions($(this).val(), classSelect.val());
            });

            $(document).on('click', '.js-question-bank-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('questionBank');
                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }
                modal.show();
            });

            $(document).on('click', '.js-question-bank-delete-trigger', function() {
                const payload = $(this).data('questionBank');
                deleteForm.data('deletingId', payload.id);
                $('#questionBankDeleteName').text(payload.name || 'this question bank');
                deleteModal.show();
            });

            $('#questionBankModal').on('hidden.bs.modal', resetCreateMode);
            $('#questionBankDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#questionBankDeleteName').text('this question bank');
            });

            rebuildClassOptions(subjectSelect.val(), classSelect.val());

            bindAcademicAjaxForm({
                formSelector: '#questionBankForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm.data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving question bank...',
                successTitle: 'Question bank saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#questionBankDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting question bank...',
                successTitle: 'Question bank deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
