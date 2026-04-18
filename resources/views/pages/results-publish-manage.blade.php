@extends('layouts.app')

@php
    $publicationRedirectUrl = route('academics.results.publish.manage', [
        'class_id' => $classId,
        'academic_year_id' => $academicYearId,
        'academic_term_id' => $academicTermId,
    ]);
    $publicationPublishUrl = route('v1.academics.results.publish', [], false);
    $publicationUnpublishUrl = route('v1.academics.results.unpublish', [], false);
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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Publish Results</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/results" class="text-secondary-light hover-text-primary hover-underline">/ Results</a>
                <a href="{{ route('academics.results.publish', ['academic_year_id' => $academic_year['id'] ?? null, 'academic_term_id' => $academic_term['id'] ?? null]) }}"
                    class="text-secondary-light hover-text-primary hover-underline">/ Publication</a>
                <span class="text-secondary-light">/ Manage</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-24">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light small mb-1">Class</div>
                    <div class="fw-semibold">{{ $class['name'] ?? 'Class' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light small mb-1">Academic Year</div>
                    <div class="fw-semibold">{{ $academic_year['name'] ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light small mb-1">Term</div>
                    <div class="fw-semibold">{{ $academic_term['name'] ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light small mb-1">Students</div>
                    <div class="fw-semibold">{{ $summary['student_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-secondary-light small mb-1">Subjects</div>
                    <div class="fw-semibold">{{ $summary['subject_count'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h6 class="mb-1">Student Publication Queue</h6>
                <p class="text-secondary-light mb-0">Select all students or only the students you want to publish or
                    unpublish.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary js-toggle-selection" data-mode="publish">Select
                    Publishable</button>
                <button type="button" class="btn btn-outline-secondary js-toggle-selection" data-mode="unpublish">Select
                    Published</button>
                <button type="button" class="btn btn-primary-600 js-submit-publication" data-action="publish">Publish
                    Selected</button>
                <button type="button" class="btn btn-outline-danger js-submit-publication"
                    data-action="unpublish">Unpublish Selected</button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="alert alert-info border-0 border-bottom rounded-0 mb-0">
                Publishing is only allowed when every active student in this class has a result record and every class
                subject has been assessed for the selected term.
            </div>
            <div class="table-responsive">
                <table class="table bordered-table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th style="width: 56px;">
                                <input type="checkbox" class="form-check-input" id="toggleAllPublicationRows">
                            </th>
                            <th>Rank</th>
                            <th>Student</th>
                            <th>Subjects</th>
                            <th>Average</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($students ?? []) as $student)
                            @php
                                $status = $student['status'] ?? 'missing';
                                $canPublish =
                                    !empty($student['result_id']) && in_array($status, ['draft', 'under_review'], true);
                                $canUnpublish = !empty($student['result_id']) && $status === 'published';
                            @endphp
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input js-publication-row"
                                        value="{{ $student['result_id'] }}" data-publishable="{{ $canPublish ? '1' : '0' }}"
                                        data-unpublishable="{{ $canUnpublish ? '1' : '0' }}" @disabled(empty($student['result_id']))>
                                </td>
                                <td>{{ $student['rank'] ?? '—' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $student['student_name'] ?? 'Student' }}</div>
                                    <div class="small text-secondary-light">{{ $student['admission_number'] ?? '—' }}</div>
                                </td>
                                <td>{{ $student['subjects_recorded'] ?? 0 }} / {{ $student['subject_count'] ?? 0 }}</td>
                                <td>{{ number_format((float) ($student['average_score'] ?? 0), 2) }}</td>
                                <td>
                                    @if ($status === 'published')
                                        <span class="badge bg-success-focus text-success-main">Published</span>
                                    @elseif ($status === 'under_review')
                                        <span class="badge bg-info-focus text-info-main">Under Review</span>
                                    @elseif ($status === 'draft')
                                        <span class="badge bg-secondary-focus text-secondary-main">Draft</span>
                                    @else
                                        <span class="badge bg-warning-focus text-warning-main">Missing Result</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary js-view-student-result-detail"
                                        data-student-id="{{ $student['student_id'] ?? '' }}">
                                        View Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-24 text-secondary-light">No students found for this
                                    class and term.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="results-panel-overlay"></div>

    <div id="studentResultDetailPanel" class="results-panel">
        <div class="px-20 py-12 border-bottom d-flex align-items-center justify-content-between gap-20">
            <div>
                <h5 class="text-lg mb-1" id="studentResultDetailTitle">Student Result Details</h5>
                <p class="mb-0 results-panel-subtitle" id="studentResultDetailMeta">Assessment record</p>
            </div>
            <button type="button" class="text-danger-600 text-lg d-flex border-0 bg-transparent"
                data-results-close="studentResultDetailPanel">
                <i class="ri-close-large-line"></i>
            </button>
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

            const publishUrl = @json($publicationPublishUrl);
            const unpublishUrl = @json($publicationUnpublishUrl);
            const redirectUrl = @json($publicationRedirectUrl);
            const students = @json($students ?? []);

            const rowCheckboxes = () => $('.js-publication-row:not(:disabled)');
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

            const collectIds = (mode) => rowCheckboxes().filter(function() {
                if (!this.checked) {
                    return false;
                }

                return mode === 'publish' ?
                    $(this).data('publishable') === 1 || $(this).data('publishable') === '1' :
                    $(this).data('unpublishable') === 1 || $(this).data('unpublishable') === '1';
            }).map(function() {
                return this.value;
            }).get();

            const setSelection = (mode) => {
                rowCheckboxes().each(function() {
                    const allowed = mode === 'publish' ?
                        ($(this).data('publishable') === 1 || $(this).data('publishable') === '1') :
                        ($(this).data('unpublishable') === 1 || $(this).data('unpublishable') === '1');

                    this.checked = allowed;
                });
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
                    (Array.isArray(assessment.components) ? assessment.components : []).forEach((
                        component) => {
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
                    const title = component.code ?
                        `${component.code || component.label}` :
                        component.label;

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
                        (Array.isArray(assessment.components) ? assessment.components : []).map((
                            component) => [
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

            $(document).on('click', '[data-results-close]', closePanels);
            $(document).on('click', '.results-panel-overlay', closePanels);

            $('#toggleAllPublicationRows').on('change', function() {
                rowCheckboxes().prop('checked', this.checked);
            });

            $(document).on('click', '.js-toggle-selection', function() {
                setSelection($(this).data('mode'));
            });

            $(document).on('click', '.js-submit-publication', function() {
                const action = $(this).data('action');
                const ids = collectIds(action);

                if (!ids.length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'No students selected',
                        text: action === 'publish' ?
                            'Select at least one draft or under-review result to publish.' :
                            'Select at least one published result to unpublish.'
                    });
                    return;
                }

                Swal.fire({
                    title: action === 'publish' ? 'Publishing results...' : 'Unpublishing results...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: action === 'publish' ? publishUrl : unpublishUrl,
                    method: 'POST',
                    data: {
                        result_ids: ids
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: action === 'publish' ? 'Results published' :
                            'Results unpublished',
                        text: response?.message || 'Action completed successfully.'
                    }).then(() => {
                        window.location.href = redirectUrl;
                    });
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action failed',
                        text: xhr?.responseJSON?.message || 'Unable to complete this action.'
                    });
                });
            });

            $(document).on('click', '.js-view-student-result-detail', function() {
                const studentId = String($(this).data('student-id') || '');
                const student = students.find((item) => String(item.student_id || '') === studentId);

                if (!student) {
                    return;
                }

                renderStudentDetail(student);
                openPanel('studentResultDetailPanel');
            });
        })(window.jQuery);
    </script>
@endpush
