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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Add Question</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/question-banks" class="text-secondary-light hover-text-primary hover-underline">/ Question Banks</a>
                <span class="text-secondary-light">/ Add Question</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="questionCreateForm" class="row g-3">
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
                    <div class="form-text" id="questionBankHelp">Question banks are filtered by the selected subject.</div>
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
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Save</button>
                    <a href="/academics/question-banks" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#questionCreateForm');
            const subjectSelect = form.find('[name="subject_id"]');
            const bankSelect = form.find('[name="question_bank_id"]');
            const bankHelp = $('#questionBankHelp');
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

            rebuildBankOptions(subjectSelect.val(), bankSelect.val());

            bindAcademicAjaxForm({
                formSelector: '#questionCreateForm',
                url: @json(Route::has('v1.academics.questions.store') ? route('v1.academics.questions.store', [], false) : null),
                method: 'POST',
                loadingText: 'Saving question...',
                successTitle: 'Question saved',
                redirectUrl: '/academics/questions',
                buildPayload: function(form, serializeForm) {
                    const payload = serializeForm(form);

                    if (payload.options) {
                        try {
                            payload.options = JSON.parse(payload.options);
                        } catch (error) {
                            throw new Error('Options must be valid JSON.');
                        }
                    } else {
                        payload.options = null;
                    }

                    return payload;
                }
            });
        })(window.jQuery);
    </script>
@endpush
