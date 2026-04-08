@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Edit Term</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/terms" class="text-secondary-light hover-text-primary hover-underline">/ Academic Terms</a>
                <span class="text-secondary-light">/ Edit</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="academicTermEditForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $term?->name) }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Academic Year</label>
                    <select class="form-select" name="academic_year_id">
                        <option value="">Select Academic Year</option>
                        @foreach (($academicYears ?? collect()) as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected(old('academic_year_id', $term?->academic_year_id) === $academicYear->id)>{{ $academicYear->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ old('start_date', $term?->start_date?->format('Y-m-d')) }}" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $term?->end_date?->format('Y-m-d')) }}" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        @foreach (($statuses ?? ['active', 'inactive', 'closed']) as $status)
                            <option value="{{ $status }}" @selected(old('status', $term?->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Update</button>
                    <a href="/academics/terms" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#academicTermEditForm',
            url: @json($term && Route::has('v1.academics.terms.update') ? route('v1.academics.terms.update', $term->id, false) : null),
            method: 'PUT',
            loadingText: 'Updating term...',
            successTitle: 'Term updated',
            redirectUrl: '/academics/terms'
        });
    </script>
@endpush
