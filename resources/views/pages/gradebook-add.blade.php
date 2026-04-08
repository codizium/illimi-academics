@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Add Gradebook Entry</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/gradebook" class="text-secondary-light hover-text-primary hover-underline">/ Gradebook</a>
                <span class="text-secondary-light">/ Add</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="gradebookCreateForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Student</label>
                    <select class="form-select" name="student_id">
                        <option value="">Select Student</option>
                        @foreach (($students ?? collect()) as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name ?? trim(($student->first_name ?? '').' '.($student->last_name ?? '')) }}</option>
                        @endforeach
                    </select>
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
                <div class="col-md-4">
                    <label class="form-label">Class</label>
                    <select class="form-select" name="class_id">
                        <option value="">Select Class</option>
                        @foreach (($classes ?? collect()) as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Component</label>
                    <select class="form-select" name="component">
                        @foreach (($components ?? []) as $component)
                            <option value="{{ $component->value }}">{{ $component->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Score</label>
                    <input type="number" class="form-control" name="score" step="0.01" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Max Score</label>
                    <input type="number" class="form-control" name="max_score" step="0.01" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Weight (%)</label>
                    <input type="number" class="form-control" name="weight" step="0.01" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Session</label>
                    <input type="text" class="form-control" name="academic_session" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Term</label>
                    <input type="text" class="form-control" name="term" />
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Save</button>
                    <a href="/academics/gradebook" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#gradebookCreateForm',
            url: @json(Route::has('v1.academics.gradebook.store') ? route('v1.academics.gradebook.store', [], false) : null),
            method: 'POST',
            loadingText: 'Saving gradebook entry...',
            successTitle: 'Gradebook entry saved',
            redirectUrl: '/academics/gradebook'
        });
    </script>
@endpush
