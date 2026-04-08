@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Exam Attempts</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/exams" class="text-secondary-light hover-text-primary hover-underline">/ Exams</a>
                <span class="text-secondary-light">/ Attempts</span>
            </div>
        </div>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search attempts..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="examAttemptsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="examAttemptsTableBody">
                        @forelse (($attempts ?? collect()) as $attempt)
                            <tr>
                                <td>{{ $attempt->student?->full_name ?? $attempt->student_id ?? '—' }}</td>
                                <td>{{ $attempt->score ?? '—' }}</td>
                                <td>{{ $attempt->status ?: '—' }}</td>
                                <td>{{ $attempt?->started_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td>{{ $attempt?->submitted_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td><a href="/academics/exams" class="text-primary-600">View</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-24 text-secondary-light">No attempts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
