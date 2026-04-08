@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Add Subject</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/subjects" class="text-secondary-light hover-text-primary hover-underline">/ Subjects</a>
                <span class="text-secondary-light">/ Add</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="subjectCreateForm" class="row g-3">
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
                        <input class="form-check-input" type="checkbox" name="is_compulsory" value="1" id="isCompulsory" />
                        <label class="form-check-label" for="isCompulsory">
                            Required for all students
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
                    <select class="form-select js-subject-classes" name="class_ids[]" multiple>
                        @foreach ($classes ?? collect() as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <input type="text" class="form-control" name="description" />
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Save</button>
                    <a href="/academics/subjects" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function() {
            const initChoices = (selector, placeholder) => {
                const element = document.querySelector(selector);
                if (!element || typeof window.Choices !== 'function') {
                    return null;
                }

                return new window.Choices(element, {
                    removeItemButton: true,
                    shouldSort: false,
                    searchResultLimit: 10,
                    placeholder: true,
                    placeholderValue: placeholder,
                    itemSelectText: '',
                });
            };

            initChoices('.js-subject-teachers', 'Select teachers');
            initChoices('.js-subject-classes', 'Select classes');
        })();

        bindAcademicAjaxForm({
            formSelector: '#subjectCreateForm',
            url: @json(Route::has('v1.academics.subjects.store') ? route('v1.academics.subjects.store', [], false) : null),
            method: 'POST',
            buildPayload: (form, serializeForm) => {
                const payload = serializeForm(form);
                payload.teacher_ids = form.find('[name="teacher_ids[]"]').val() || [];
                payload.class_ids = form.find('[name="class_ids[]"]').val() || [];
                return payload;
            },
            loadingText: 'Saving subject...',
            successTitle: 'Subject saved',
            redirectUrl: '/academics/subjects'
        });
    </script>
@endpush
