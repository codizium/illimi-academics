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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Create Exam</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/exams" class="text-secondary-light hover-text-primary hover-underline">/ Exams</a>
                <span class="text-secondary-light">/ Create</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="examCreateForm" class="row g-3">
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
                    <div class="form-text" id="examClassHelp">Select a subject to load its assigned classes.</div>
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
                    <label class="form-label">Session</label>
                    <input type="text" class="form-control" name="academic_session" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Term</label>
                    <input type="text" class="form-control" name="term" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Starts At</label>
                    <input type="datetime-local" class="form-control" name="starts_at" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ends At</label>
                    <input type="datetime-local" class="form-control" name="ends_at" />
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Save</button>
                    <a href="/academics/exams" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#examCreateForm');
            const subjectSelect = form.find('[name="subject_id"]');
            const classSelect = form.find('[name="class_id"]');
            const classHelp = $('#examClassHelp');
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

            subjectSelect.on('change', function() {
                rebuildClassOptions($(this).val(), classSelect.val());
            });

            rebuildClassOptions(subjectSelect.val(), classSelect.val());

            bindAcademicAjaxForm({
                formSelector: '#examCreateForm',
                url: @json(Route::has('v1.academics.exams.store') ? route('v1.academics.exams.store', [], false) : null),
                method: 'POST',
                loadingText: 'Saving exam...',
                successTitle: 'Exam saved',
                redirectUrl: '/academics/exams'
            });
        })(window.jQuery);
    </script>
@endpush
