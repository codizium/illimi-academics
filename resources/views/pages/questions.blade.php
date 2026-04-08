@extends('layouts.app')

@section('content')
    @php
        $allQuestionBanks = ($questionBanks ?? collect())
            ->map(fn ($questionBank) => [
                'id' => $questionBank->id,
                'name' => $questionBank->name,
                'subject_id' => $questionBank->subject_id,
                'subject_name' => $questionBank->subject?->name,
            ])
            ->values();
        $questionBankMap = $allQuestionBanks->mapWithKeys(fn ($bank) => [$bank['id'] => $bank])->all();
    @endphp
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Questions</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Questions</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-question-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Question
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Question Directory</h6>
                    <p class="mb-0 text-secondary-light">Create and manage questions from one live-updating list.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search questions..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="questionsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Question</th>
                            <th>Bank</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Difficulty</th>
                            <th>Marks</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($questions ?? collect()) as $question)
                            @php
                                $payload = [
                                    'id' => $question->id,
                                    'question_bank_id' => $question->question_bank_id,
                                    'question_bank_name' => $question->questionBank?->name,
                                    'subject_id' => $question->subject_id,
                                    'subject_name' => $question->subject?->name,
                                    'type' => $question->type?->value ?? $question->type,
                                    'difficulty' => $question->difficulty,
                                    'marks' => $question->marks !== null ? (float) $question->marks : null,
                                    'content' => $question->content,
                                    'options' => $question->options,
                                    'correct_answer' => $question->correct_answer,
                                    'explanation' => $question->explanation,
                                ];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($question->content, 60) }}</td>
                                <td>{{ $question->questionBank?->name ?? '—' }}</td>
                                <td>{{ $question->subject?->name ?? '—' }}</td>
                                <td>{{ $question->type?->label() ?? $question->type ?? '—' }}</td>
                                <td>{{ ucfirst($question->difficulty ?? '—') }}</td>
                                <td>{{ $question->marks ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-question-modal-trigger"
                                            data-mode="edit" data-question='@json($payload)'>Edit</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-question-delete-trigger"
                                            data-question='@json($payload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-24 text-secondary-light">No questions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="questionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="questionModalTitle">Add Question</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="questionModalSubtitle">Fill in the question details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="questionForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.questions.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.questions.update', ['id' => '__ID__'], false) }}">
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
                            <label class="form-label">Question Bank</label>
                            <select class="form-select" name="question_bank_id">
                                <option value="">Select Bank</option>
                                @foreach (($questionBanks ?? collect()) as $questionBank)
                                    <option value="{{ $questionBank->id }}">{{ $questionBank->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text" id="questionModalBankHelp">Question banks are filtered by the selected subject.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type">
                                @foreach (($questionTypes ?? []) as $questionType)
                                    <option value="{{ $questionType->value }}">{{ $questionType->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Difficulty</label>
                            <select class="form-select" name="difficulty">
                                @foreach (($difficulties ?? ['easy', 'medium', 'hard']) as $difficulty)
                                    <option value="{{ $difficulty }}">{{ ucfirst($difficulty) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marks</label>
                            <input type="number" class="form-control" name="marks" step="0.01" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" rows="4" name="content"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Options (JSON)</label>
                            <textarea class="form-control" rows="3" name="options"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Correct Answer</label>
                            <input type="text" class="form-control" name="correct_answer" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Explanation</label>
                            <textarea class="form-control" rows="3" name="explanation"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="questionForm" class="btn btn-primary-600" id="questionSubmitButton">Save Question</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="questionDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete this question?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="questionDeleteForm" data-delete-url-template="{{ route('v1.academics.questions.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="questionDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#questionForm');
            const deleteForm = $('#questionDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('questionModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('questionDeleteModal'));
            const subjectSelect = form.find('[name="subject_id"]');
            const bankSelect = form.find('[name="question_bank_id"]');
            const bankHelp = $('#questionModalBankHelp');
            const allQuestionBanks = @json($allQuestionBanks);
            const questionBankMap = @json($questionBankMap);

            const rebuildBankOptions = (subjectId, selectedValue = '', selectedLabel = '') => {
                const selectedId = selectedValue ? String(selectedValue) : '';
                const availableBanks = allQuestionBanks.filter((item) => !subjectId || !item.subject_id || item.subject_id === subjectId);
                let hasSelectedOption = false;

                bankSelect.empty().append(new Option('Select Bank', ''));

                availableBanks.forEach((item) => {
                    const isSelected = selectedId !== '' && String(item.id) === selectedId;
                    if (isSelected) {
                        hasSelectedOption = true;
                    }

                    bankSelect.append(new Option(item.name, item.id, false, isSelected));
                });

                if (selectedId && !hasSelectedOption) {
                    bankSelect.append(new Option(selectedLabel || 'Current bank', selectedId, false, true));
                }

                bankHelp.text(subjectId ? 'Question banks now match the selected subject.' : 'Question banks are filtered by the selected subject.');
            };

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                $('#questionModalTitle').text('Add Question');
                $('#questionModalSubtitle').text('Fill in the question details and save.');
                $('#questionSubmitButton').text('Save Question');
                form.find('[name="difficulty"]').val('easy');
                rebuildBankOptions('', '');
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                form.data('method', 'PUT');
                $('#questionModalTitle').text('Edit Question');
                $('#questionModalSubtitle').text('Update the question details and save your changes.');
                $('#questionSubmitButton').text('Update Question');
                form.find('[name="subject_id"]').val(payload.subject_id || '');
                rebuildBankOptions(payload.subject_id || '', payload.question_bank_id || '', payload.question_bank_name || '');
                form.find('[name="type"]').val(payload.type || '');
                form.find('[name="difficulty"]').val(payload.difficulty || 'easy');
                form.find('[name="marks"]').val(payload.marks || '');
                form.find('[name="content"]').val(payload.content || '');
                form.find('[name="options"]').val(payload.options ? JSON.stringify(payload.options, null, 2) : '');
                form.find('[name="correct_answer"]').val(payload.correct_answer || '');
                form.find('[name="explanation"]').val(payload.explanation || '');
            };

            subjectSelect.on('change', function() {
                rebuildBankOptions($(this).val(), bankSelect.val());
            });

            bankSelect.on('change', function() {
                const bank = questionBankMap[$(this).val()];
                if (!bank?.subject_id) {
                    return;
                }

                subjectSelect.val(bank.subject_id);
                rebuildBankOptions(bank.subject_id, bank.id, bank.name);
            });

            $(document).on('click', '.js-question-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('question');
                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }
                modal.show();
            });

            $(document).on('click', '.js-question-delete-trigger', function() {
                const payload = $(this).data('question');
                deleteForm.data('deletingId', payload.id);
                deleteModal.show();
            });

            $('#questionModal').on('hidden.bs.modal', resetCreateMode);
            $('#questionDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
            });

            rebuildBankOptions(subjectSelect.val(), bankSelect.val());

            bindAcademicAjaxForm({
                formSelector: '#questionForm',
                url: (currentForm) => {
                    const id = currentForm.data('editingId');
                    return id ? currentForm.data('updateUrlTemplate').replace('__ID__', id) : currentForm.data('createUrl');
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving question...',
                successTitle: 'Question saved',
                buildPayload: function(currentForm, serializeForm) {
                    const payload = serializeForm(currentForm);
                    if (payload.options) {
                        payload.options = JSON.parse(payload.options);
                    } else {
                        payload.options = null;
                    }
                    return payload;
                },
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#questionDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting question...',
                successTitle: 'Question deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
