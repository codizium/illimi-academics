@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Transcripts</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Transcripts</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="transcriptGenerateForm" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Student</label>
                    <select class="form-select" name="student_id">
                        <option value="">Select Student</option>
                        @foreach (($students ?? collect()) as $student)
                            <option value="{{ $student->id }}">{{ $student->full_name ?? trim(($student->first_name ?? '').' '.($student->last_name ?? '')) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Session</label>
                    <input type="text" class="form-control" name="academic_session" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Term</label>
                    <input type="text" class="form-control" name="term" placeholder="Optional" />
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-600">Generate Transcript</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-24">
        <div class="card-body p-0">
            <div class="px-20 py-16 border-bottom border-neutral-200">
                <h6 class="mb-0">Generated Transcripts</h6>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th>Generated</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($transcripts ?? collect()) as $transcript)
                            <tr>
                                <td>{{ $transcript->student?->full_name ?? $transcript->student_id ?? '—' }}</td>
                                <td>{{ $transcript->academic_session ?: '—' }}</td>
                                <td>{{ $transcript->term ?: '—' }}</td>
                                <td>{{ $transcript?->generated_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td>{{ $transcript->file_path ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-24 text-secondary-light">No transcripts generated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        bindAcademicAjaxForm({
            formSelector: '#transcriptGenerateForm',
            url: function(form) {
                const studentId = form.find('[name="student_id"]').val();

                if (!studentId) {
                    throw new Error('Select a student before generating a transcript.');
                }

                return `/api/v1/academics/transcripts/${studentId}/generate`;
            },
            method: 'POST',
            loadingText: 'Generating transcript...',
            successTitle: 'Transcript generated',
            reloadOnSuccess: true,
            buildPayload: function(form, serializeForm) {
                const payload = serializeForm(form);

                if (!payload.student_id) {
                    throw new Error('Select a student before generating a transcript.');
                }

                delete payload.student_id;

                return payload;
            }
        });
    </script>
@endpush
