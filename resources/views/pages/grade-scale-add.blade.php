@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Add Grade Scale</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/grade-scales" class="text-secondary-light hover-text-primary hover-underline">/ Grade Scales</a>
                <span class="text-secondary-light">/ Add</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="gradeScaleCreateForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Standard Scale" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-control" name="code" placeholder="STD" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Default</label>
                    <select class="form-select" name="is_default">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Save</button>
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
            formSelector: '#gradeScaleCreateForm',
            url: @json(Route::has('v1.academics.grade_scales.store') ? route('v1.academics.grade_scales.store', [], false) : null),
            method: 'POST',
            loadingText: 'Saving grade scale...',
            successTitle: 'Grade scale saved',
            redirectUrl: '/academics/grade-scales'
        });
    </script>
@endpush
