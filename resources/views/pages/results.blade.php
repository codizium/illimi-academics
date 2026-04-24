@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Results</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Results</span>
            </div>
        </div>
        <a href="/academics/results/publish" class="btn btn-primary-600 d-flex align-items-center gap-6">
            <span class="d-flex text-md"><i class="ri-send-plane-2-line"></i></span>
            Publish Results
        </a>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search results..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="resultsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Term</th>
                            <th>Total</th>
                            {{-- <th>Grade</th> --}}
                            <th>Status</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                    <tbody id="resultsTableBody">
                        @forelse (($results ?? collect()) as $result)
                            <tr>
                                <td>{{ $result->student?->full_name ?? $result->student_id ?? '—' }}</td>
                                <td>{{ $result->academicClass?->name ?? $result->class_id ?? '—' }}</td>
                                <td>{{ $result->academic_session ?: '—' }}</td>
                                <td>{{ $result->term ?: '—' }}</td>
                                <td>{{ $result->total_score ?? '—' }}</td>
                                {{-- <td>{{ $result->grade ?: '—' }}</td> --}}
                                <td>{{ $result->status?->label() ?? $result->status ?? '—' }}</td>
                                {{-- <td><a href="/academics/results" class="text-primary-600">View</a></td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-24 text-secondary-light">No results found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
