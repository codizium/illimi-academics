@extends('layouts.app')

@php
    $selectedAcademicYear = collect($academicYears ?? [])->firstWhere('id', $selectedAcademicYearId ?? null);
    $selectedAcademicTerm = collect($academicTerms ?? [])->firstWhere('id', $selectedAcademicTermId ?? null);
@endphp

@push('styles')
    <style>
        .results-panel-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.38);
            z-index: 98;
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s ease, visibility .3s ease;
        }

        .results-panel-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .results-panel {
            position: fixed;
            top: 0;
            right: 0;
            width: min(920px, 100%);
            height: 100vh;
            background: #fff;
            z-index: 99;
            transform: translateX(100%);
            transition: transform .3s ease;
            overflow-y: auto;
            box-shadow: -12px 0 35px rgba(15, 23, 42, 0.14);
        }

        .results-panel.active {
            transform: translateX(0);
        }

        .results-panel-subtitle {
            color: #64748b;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Result Publication</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/results" class="text-secondary-light hover-text-primary hover-underline">/ Results</a>
                <span class="text-secondary-light">/ Publication</span>
            </div>
        </div>
    </div>

    <div class="card mb-24">
        <div class="card-body">
            <form method="GET" action="{{ route('academics.results.publish') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Academic Year</label>
                    <select class="form-select" name="academic_year_id" onchange="this.form.submit()">
                        @foreach ($academicYears ?? collect() as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected(($selectedAcademicYearId ?? null) === $academicYear->id)>
                                {{ $academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Term</label>
                    <select class="form-select" name="academic_term_id" onchange="this.form.submit()">
                        @foreach ($academicTerms ?? collect() as $academicTerm)
                            <option value="{{ $academicTerm->id }}" @selected(($selectedAcademicTermId ?? null) === $academicTerm->id)>
                                {{ $academicTerm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-600 w-100">Refresh</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table bordered-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Students</th>
                            <th>Subjects</th>
                            <th>Ready Students</th>
                            <th>Published</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($classSummaries ?? collect()) as $summary)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $summary['class_name'] }}</div>
                                    {{-- <div class="text-secondary-light small">{{ $summary['level'] ?: ($summary['class_section_name'] ?: 'Class') }}</div> --}}
                                </td>
                                <td>{{ $summary['student_count'] }}</td>
                                <td>{{ $summary['subject_count'] }}</td>
                                <td>{{ $summary['ready_students_count'] }} / {{ $summary['student_count'] }}</td>
                                <td>{{ $summary['published_students_count'] }}</td>
                                <td>
                                    @if ($summary['can_publish'])
                                        <span class="badge bg-success-focus text-success-main">Ready</span>
                                    @else
                                        <span class="badge bg-warning-focus text-warning-main">Incomplete</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-primary-600 btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button type="button" class="dropdown-item js-preview-publication"
                                                    data-class-id="{{ $summary['class_id'] }}"
                                                    data-class-name="{{ $summary['class_name'] }}">
                                                    View Result Summary
                                                </button>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('academics.results.publish.manage', [
                                                        'class_id' => $summary['class_id'],
                                                        'academic_year_id' => $selectedAcademicYearId,
                                                        'academic_term_id' => $selectedAcademicTermId,
                                                    ]) }}">
                                                    Publish Result
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-24 text-secondary-light">No classes found for the
                                    selected scope.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="results-panel-overlay"></div>

    <div id="publicationPreviewPanel" class="results-panel">
        <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
            <div>
                <h5 class="text-lg mb-1">Result Summary</h5>
                <p class="mb-0 results-panel-subtitle" id="publicationPreviewScope">Loading...</p>
            </div>
            <button type="button" class="text-danger-600 text-lg d-flex border-0 bg-transparent"
                data-results-close="publicationPreviewPanel">
                <i class="ri-close-large-line"></i>
            </button>
        </div>
        <div class="p-20">
            <div id="publicationPreviewLoading" class="text-center py-32">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div id="publicationPreviewContent" class="d-none">
                <div class="row g-3 mb-20">
                    <div class="col-md-3">
                        <div class="border rounded p-16 h-100">
                            <div class="text-secondary-light small">Students</div>
                            <div class="fw-semibold fs-5" id="previewStudentsCount">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-16 h-100">
                            <div class="text-secondary-light small">Subjects</div>
                            <div class="fw-semibold fs-5" id="previewSubjectsCount">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-16 h-100">
                            <div class="text-secondary-light small">Ready</div>
                            <div class="fw-semibold fs-5" id="previewReadyCount">0</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-16 h-100">
                            <div class="text-secondary-light small">Published</div>
                            <div class="fw-semibold fs-5" id="previewPublishedCount">0</div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table bordered-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student</th>
                                <th>Subjects</th>
                                <th>Average</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="publicationPreviewBody"></tbody>
                    </table>
                </div>
            </div>
            <div id="publicationPreviewEmpty" class="d-none text-center py-32 text-secondary-light">
                No student result summary found for this class.
            </div>
        </div>
    </div>

    <div id="studentResultDetailPanel" class="results-panel">
        <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
            <div>
                <h5 class="text-lg mb-1" id="studentResultDetailTitle">Student Result Details</h5>
                <p class="mb-0 results-panel-subtitle" id="studentResultDetailMeta">Assessment record</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary"
                    data-results-back="publicationPreviewPanel">Back</button>
                <button type="button" class="text-danger-600 text-lg d-flex border-0 bg-transparent"
                    data-results-close="studentResultDetailPanel">
                    <i class="ri-close-large-line"></i>
                </button>
            </div>
        </div>
        <div class="p-20">
            <div class="table-responsive">
                <table class="table bordered-table mb-0 align-middle">
                    <thead id="studentResultDetailHead"></thead>
                    <tbody id="studentResultDetailBody"></tbody>
                </table>
            </div>
            <div id="studentResultDetailEmpty" class="d-none text-center py-32 text-secondary-light">
                No assessment records found for this student.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function($) {
            if (!$) {
                return;
            }

            const previewUrl = @json(route('v1.academics.results.publication_preview', [], false));
            const academicYearId = @json($selectedAcademicYearId);
            const academicTermId = @json($selectedAcademicTermId);
            let currentPreviewStudents = [];

            const escapeHtml = (value) => $('<div>').text(value ?? '').html();
            const overlay = $('.results-panel-overlay');

            const openPanel = (panelId) => {
                $('.results-panel').removeClass('active');
                $('#' + panelId).addClass('active');
                overlay.addClass('active');
            };

            const closePanels = () => {
                $('.results-panel').removeClass('active');
                overlay.removeClass('active');
            };

            const setPreviewLoadingState = () => {
                $('#publicationPreviewLoading').removeClass('d-none');
                $('#publicationPreviewContent, #publicationPreviewEmpty').addClass('d-none');
                $('#publicationPreviewBody').html('');
                currentPreviewStudents = [];
            };

            const renderStudentDetail = (student) => {
                const assessments = Array.isArray(student?.assessments) ? student.assessments : [];

                $('#studentResultDetailTitle').text(student?.student_name || 'Student Result Details');
                $('#studentResultDetailMeta').text(
                    `${student?.subjects_recorded || 0} subjects · Average ${Number(student?.average_score || 0).toFixed(2)}`
                );

                if (!assessments.length) {
                    $('#studentResultDetailHead').html('');
                    $('#studentResultDetailBody').html('');
                    $('#studentResultDetailEmpty').removeClass('d-none');
                    return;
                }

                $('#studentResultDetailEmpty').addClass('d-none');
                const componentDefinitions = [];
                const componentKeys = new Set();

                assessments.forEach((assessment) => {
                    (Array.isArray(assessment.components) ? assessment.components : []).forEach((component) => {
                        const key = String(component.code || component.label || '').trim();

                        if (!key || componentKeys.has(key)) {
                            return;
                        }

                        componentKeys.add(key);
                        componentDefinitions.push({
                            key,
                            label: component.label || component.code || key,
                            code: component.code || ''
                        });
                    });
                });

                const headCells = componentDefinitions.map((component) => {
                    const title = component.code
                        ? `${component.code} (${component.label})`
                        : component.label;

                    return `<th>${escapeHtml(title)}</th>`;
                }).join('');

                $('#studentResultDetailHead').html(`
                    <tr>
                        <th>Subject</th>
                        ${headCells}
                        <th>Total</th>
                        <th>Grade</th>
                        <th>Rank</th>
                        <th>Remark</th>
                    </tr>
                `);

                const rows = assessments.map((assessment) => {
                    const scoreMap = new Map(
                        (Array.isArray(assessment.components) ? assessment.components : []).map((component) => [
                            String(component.code || component.label || '').trim(),
                            Number(component.score || 0).toFixed(2)
                        ])
                    );

                    const componentCells = componentDefinitions.map((component) => {
                        return `<td>${escapeHtml(scoreMap.get(component.key) || '—')}</td>`;
                    }).join('');

                    return `
                        <tr>
                            <td>${escapeHtml(assessment.subject_name || 'Subject')}</td>
                            ${componentCells}
                            <td>${Number(assessment.total_score || 0).toFixed(2)}</td>
                            <td class="text-uppercase">${escapeHtml(assessment.grade || '—')}</td>
                            <td>${assessment.subject_rank ? `${assessment.subject_rank}/${assessment.subject_participant_count || assessment.subject_rank}` : '—'}</td>
                            <td>${escapeHtml(assessment.remark || '—')}</td>
                        </tr>
                    `;
                }).join('');

                $('#studentResultDetailBody').html(rows);
            };

            const setPreviewContent = (payload) => {
                const students = Array.isArray(payload.students) ? payload.students : [];
                currentPreviewStudents = students;

                $('#publicationPreviewLoading').addClass('d-none');
                $('#publicationPreviewScope').text(
                    `${payload.class?.name || 'Class'} · ${payload.academic_year?.name || ''} · ${payload.academic_term?.name || ''}`
                    );
                $('#previewStudentsCount').text(payload.summary?.student_count || 0);
                $('#previewSubjectsCount').text(payload.summary?.subject_count || 0);
                $('#previewReadyCount').text(payload.summary?.ready_count || 0);
                $('#previewPublishedCount').text(payload.summary?.published_count || 0);

                if (!students.length) {
                    $('#publicationPreviewEmpty').removeClass('d-none');
                    return;
                }

                const rows = students.map((student) => `
                    <tr>
                        <td>${student.rank || ''}</td>
                        <td>
                            <div class="fw-semibold">${escapeHtml(student.student_name || 'Student')}</div>
                            <div class="small text-secondary-light">${escapeHtml(student.admission_number || '')}</div>
                        </td>
                        <td>${student.subjects_recorded || 0} / ${student.subject_count || 0}</td>
                        <td>${Number(student.average_score || 0).toFixed(2)}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary js-view-student-result-detail" data-student-id="${escapeHtml(student.student_id || '')}">
                                View Detail
                            </button>
                        </td>
                    </tr>
                `).join('');

                $('#publicationPreviewBody').html(rows);
                $('#publicationPreviewContent').removeClass('d-none');
            };

            $(document).on('click', '[data-results-close]', closePanels);
            $(document).on('click', '.results-panel-overlay', closePanels);
            $(document).on('click', '[data-results-back]', function() {
                openPanel($(this).data('results-back'));
            });

            $(document).on('click', '.js-preview-publication', function() {
                const classId = $(this).data('class-id');

                if (!classId || !academicYearId || !academicTermId) {
                    return;
                }

                setPreviewLoadingState();
                openPanel('publicationPreviewPanel');

                $.ajax({
                    url: previewUrl,
                    method: 'GET',
                    data: {
                        class_id: classId,
                        academic_year_id: academicYearId,
                        academic_term_id: academicTermId
                    }
                }).done(function(response) {
                    setPreviewContent(response?.data || {});
                }).fail(function(xhr) {
                    $('#publicationPreviewLoading').addClass('d-none');
                    $('#publicationPreviewEmpty')
                        .removeClass('d-none')
                        .text(xhr?.responseJSON?.message || 'Unable to load result summary.');
                });
            });

            $(document).on('click', '.js-view-student-result-detail', function() {
                const studentId = String($(this).data('student-id') || '');
                const student = currentPreviewStudents.find((item) => String(item.student_id || '') === studentId);

                if (!student) {
                    return;
                }

                renderStudentDetail(student);
                openPanel('studentResultDetailPanel');
            });
        })(window.jQuery);
    </script>
@endpush
