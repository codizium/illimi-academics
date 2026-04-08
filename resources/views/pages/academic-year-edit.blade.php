@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Edit Academic Year</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/academic-years" class="text-secondary-light hover-text-primary hover-underline">/ Academic Years</a>
                <span class="text-secondary-light">/ Edit</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="academicYearEditForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $academicYear?->name) }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $academicYear?->slug) }}" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" value="{{ old('start_date', $academicYear?->start_date?->format('Y-m-d')) }}" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $academicYear?->end_date?->format('Y-m-d')) }}" />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        @foreach (($statuses ?? ['active', 'inactive', 'closed']) as $status)
                            <option value="{{ $status }}" @selected(old('status', $academicYear?->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $academicYear?->description) }}</textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Update</button>
                    <a href="/academics/academic-years" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#academicYearEditForm',
            url: @json($academicYear && Route::has('v1.academics.academic_years.update') ? route('v1.academics.academic_years.update', $academicYear->id, false) : null),
            method: 'PUT',
            loadingText: 'Updating academic year...',
            successTitle: 'Academic year updated',
            redirectUrl: '/academics/academic-years'
        });
    </script>
@endpush
