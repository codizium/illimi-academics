@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Add Class</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/classes" class="text-secondary-light hover-text-primary hover-underline">/ Classes</a>
                <span class="text-secondary-light">/ Add</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="classCreateForm" action="{{ route('v1.academics.classes.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" placeholder="JSS 1" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Level</label>
                    <input type="text" class="form-control" name="level" placeholder="Junior" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Section</label>
                    <select class="form-select" name="section_id">
                        <option value="">Select Section</option>
                        @foreach ($sections ?? collect() as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Capacity</label>
                    <input type="number" class="form-control" name="capacity" placeholder="40" />
                </div>
                <div class="col-md-8">
                    <label class="form-label">Class Teacher</label>
                    <select class="form-select" name="class_teacher_id">
                        <option value="">Select Staff</option>
                        @foreach ($teachers ?? collect() as $teacher)
                            <option value="{{ $teacher->id }}">
                                {{ $teacher->full_name ?? trim(($teacher->first_name ?? '') . ' ' . ($teacher->last_name ?? '')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Save</button>
                    <a href="/academics/classes" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#classCreateForm',
            url: @json(Route::has('v1.academics.classes.store') ? route('v1.academics.classes.store', [], false) : null),
            method: 'POST',
            loadingText: 'Saving class...',
            successTitle: 'Class saved',
            redirectUrl: '/academics/classes'
        });
    </script>
@endpush
