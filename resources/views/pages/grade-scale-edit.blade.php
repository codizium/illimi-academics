@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Edit Grade Scale</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/grade-scales" class="text-secondary-light hover-text-primary hover-underline">/ Grade Scales</a>
                <span class="text-secondary-light">/ Edit</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="gradeScaleEditForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $gradeScale?->name) }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-control" name="code" value="{{ old('code', $gradeScale?->code) }}" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Default</label>
                    <select class="form-select" name="is_default">
                        <option value="0" @selected((string) old('is_default', (int) ($gradeScale?->is_default ?? false)) === '0')>No</option>
                        <option value="1" @selected((string) old('is_default', (int) ($gradeScale?->is_default ?? false)) === '1')>Yes</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $gradeScale?->description) }}</textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Update</button>
                    <a href="/academics/grade-scales" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#gradeScaleEditForm',
            url: @json($gradeScale && Route::has('v1.academics.grade_scales.update') ? route('v1.academics.grade_scales.update', $gradeScale->id, false) : null),
            method: 'PUT',
            loadingText: 'Updating grade scale...',
            successTitle: 'Grade scale updated',
            redirectUrl: '/academics/grade-scales'
        });
    </script>
@endpush
