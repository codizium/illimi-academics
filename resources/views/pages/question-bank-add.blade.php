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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Add Question Bank</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/question-banks" class="text-secondary-light hover-text-primary hover-underline">/ Question Banks</a>
                <span class="text-secondary-light">/ Add</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="questionBankCreateForm" class="row g-3">
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
                    <div class="form-text" id="questionBankClassHelp">Choose a subject to narrow the class list.</div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="3" name="description"></textarea>
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
            const form = $('#questionBankCreateForm');
            const subjectSelect = form.find('[name="subject_id"]');
            const classSelect = form.find('[name="class_id"]');
            const classHelp = $('#questionBankClassHelp');
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

            subjectSelect.on('change', function() {
                rebuildClassOptions($(this).val(), classSelect.val());
            });

            rebuildClassOptions(subjectSelect.val(), classSelect.val());

            bindAcademicAjaxForm({
                formSelector: '#questionBankCreateForm',
                url: @json(Route::has('v1.academics.question_banks.store') ? route('v1.academics.question_banks.store', [], false) : null),
                method: 'POST',
                loadingText: 'Saving question bank...',
                successTitle: 'Question bank saved',
                redirectUrl: '/academics/question-banks'
            });
        })(window.jQuery);
    </script>
@endpush
