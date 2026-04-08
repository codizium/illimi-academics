@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Submit Appeal</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/appeals" class="text-secondary-light hover-text-primary hover-underline">/ Appeals</a>
                <span class="text-secondary-light">/ New</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="appealCreateForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Result</label>
                    <select class="form-select" name="result_id">
                        <option value="">Select Result</option>
                        @foreach (($results ?? collect()) as $result)
                            <option value="{{ $result->id }}">
                                {{ ($result->student?->full_name ?? $result->student_id ?? 'Student') }} - {{ $result->academic_session ?: 'Session' }} - {{ $result->term ?: 'Term' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Student</label>
                    <select class="form-select" name="student_id">
                        <option value="">Select Student</option>
                        @foreach (($students ?? collect()) as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name ?? trim(($student->first_name ?? '').' '.($student->last_name ?? '')) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Reason</label>
                    <textarea class="form-control" rows="4" name="reason"></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Submit</button>
                    <a href="/academics/appeals" class="btn btn-outline-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#appealCreateForm',
            url: @json(Route::has('v1.academics.appeals.store') ? route('v1.academics.appeals.store', [], false) : null),
            method: 'POST',
            loadingText: 'Submitting appeal...',
            successTitle: 'Appeal submitted',
            redirectUrl: '/academics/appeals'
        });
    </script>
@endpush
