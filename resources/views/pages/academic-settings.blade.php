@extends('layouts.app')

@php
    $statusClasses = [
        'active' => 'bg-success-focus text-success-main',
        'inactive' => 'bg-warning-focus text-warning-main',
        'closed' => 'bg-danger-focus text-danger-main',
    ];
@endphp

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Academic Settings</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Academic Settings</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-12">
            <div id="activeTabActions">
                <!-- Action buttons will be injected here by JS based on active tab -->
            </div>
        </div>
    </div>

    <div class="card h-100">
        <div class="card-header border-bottom bg-base">
            <ul class="nav nav-tabs card-header-tabs border-0" id="academicSettingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="years-tab" data-bs-toggle="tab" data-bs-target="#years" type="button" role="tab">Academic Sessions</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="terms-tab" data-bs-toggle="tab" data-bs-target="#terms" type="button" role="tab">Academic Terms</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="scales-tab" data-bs-toggle="tab" data-bs-target="#scales" type="button" role="tab">Grade Scales</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button" role="tab">Grading Templates</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="academicSettingsTabsContent">
                
                <!-- Academic Sessions Tab -->
                <div class="tab-pane fade show active" id="years" role="tabpanel">
                    <div class="p-20 border-bottom">
                        <h6 class="mb-4">Academic Sessions</h6>
                        <p class="mb-0 text-secondary-light small">Manage school years and sessions.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 data-table" id="academicYearsTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Timeline</th>
                                    <th>Terms</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($academicYears ?? collect() as $year)
                                    @php
                                        $yearPayload = [
                                            'id' => $year->id,
                                            'name' => $year->name,
                                            'slug' => $year->slug,
                                            'start_date' => $year?->start_date?->format('Y-m-d'),
                                            'end_date' => $year?->end_date?->format('Y-m-d'),
                                            'status' => $year->status,
                                            'description' => $year->description,
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-primary-light">{{ $year->name }}</div>
                                            <div class="text-sm text-secondary-light">{{ $year->slug }}</div>
                                        </td>
                                        <td>
                                            <div>{{ $year?->start_date?->format('d M, Y') ?? 'N/A' }}</div>
                                            <div class="text-sm text-secondary-light">to {{ $year?->end_date?->format('d M, Y') ?? 'Open' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge text-sm fw-medium px-12 py-4 radius-8 bg-primary-50 text-primary-600">
                                                {{ $year->terms_count }} {{ \Illuminate\Support\Str::plural('term', $year->terms_count) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $statusClasses[$year->status] ?? 'bg-neutral-100 text-neutral-600' }}">
                                                {{ ucfirst($year->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-8">
                                                <button type="button" class="btn btn-sm btn-outline-primary-600 js-academic-year-modal-trigger" data-mode="edit" data-year='@json($yearPayload)'>Edit</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger-600 js-academic-year-delete-trigger" data-year='@json($yearPayload)'>Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Academic Terms Tab -->
                <div class="tab-pane fade" id="terms" role="tabpanel">
                    <div class="p-20 border-bottom">
                        <h6 class="mb-4">Academic Terms</h6>
                        <p class="mb-0 text-secondary-light small">Manage semesters and terms for each session.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 data-table" id="academicTermsTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Academic Year</th>
                                    <th>Timeline</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($terms ?? collect() as $term)
                                    @php
                                        $termPayload = [
                                            'id' => $term->id,
                                            'academic_year_id' => $term->academic_year_id,
                                            'name' => $term->name,
                                            'slug' => $term->slug,
                                            'description' => $term->description,
                                            'start_date' => $term?->start_date?->format('Y-m-d'),
                                            'end_date' => $term?->end_date?->format('Y-m-d'),
                                            'status' => $term->status,
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-primary-light">{{ $term->name }}</div>
                                            <div class="text-sm text-secondary-light">{{ $term->slug }}</div>
                                        </td>
                                        <td>{{ $term->academicYear?->name ?? '—' }}</td>
                                        <td>
                                            <div>{{ $term?->start_date?->format('d M, Y') ?? 'N/A' }}</div>
                                            <div class="text-sm text-secondary-light">to {{ $term?->end_date?->format('d M, Y') ?? 'Open' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $statusClasses[$term->status] ?? 'bg-neutral-100 text-neutral-600' }}">
                                                {{ ucfirst($term->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-8">
                                                <button type="button" class="btn btn-sm btn-outline-primary-600 js-academic-term-modal-trigger" data-mode="edit" data-term='@json($termPayload)'>Edit</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger-600 js-academic-term-delete-trigger" data-term='@json($termPayload)'>Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Grade Scales Tab -->
                <div class="tab-pane fade" id="scales" role="tabpanel">
                    <div class="p-20 border-bottom">
                        <h6 class="mb-4">Grade Scales</h6>
                        <p class="mb-0 text-secondary-light small">Define grading criteria and point scales.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 data-table" id="gradeScalesTable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Score Range</th>
                                    <th>Default</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradeScales ?? collect() as $gradeScale)
                                    @php
                                        $gradeScalePayload = [
                                            'id' => $gradeScale->id,
                                            'name' => $gradeScale->name,
                                            'code' => $gradeScale->code,
                                            'min_score' => (float)$gradeScale->min_score,
                                            'max_score' => (float)$gradeScale->max_score,
                                            'description' => $gradeScale->description,
                                            'is_default' => (int)$gradeScale->is_default,
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold text-primary-light">{{ $gradeScale->name }}</td>
                                        <td>{{ $gradeScale->code ?: '—' }}</td>
                                        <td>{{ $gradeScale->min_score }} - {{ $gradeScale->max_score }}</td>
                                        <td>
                                            <span class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $gradeScale->is_default ? 'bg-success-focus text-success-main' : 'bg-neutral-100 text-neutral-600' }}">
                                                {{ $gradeScale->is_default ? 'Default' : 'Secondary' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-8">
                                                <button type="button" class="btn btn-sm btn-outline-primary-600 js-grade-scale-modal-trigger" data-mode="edit" data-grade-scale='@json($gradeScalePayload)'>Edit</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger-600 js-grade-scale-delete-trigger" data-grade-scale='@json($gradeScalePayload)'>Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Grading Templates Tab -->
                <div class="tab-pane fade" id="templates" role="tabpanel">
                    <div class="p-20 border-bottom">
                        <h6 class="mb-4">Grading Templates</h6>
                        <p class="mb-0 text-secondary-light small">Configure assessment structures for different subjects and classes.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table bordered-table mb-0 data-table" id="gradebookTemplatesTable">
                            <thead>
                                <tr>
                                    <th>Template</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($templates ?? collect() as $template)
                                    @php
                                        $templatePayload = [
                                            'id' => $template->id,
                                            'name' => $template->name,
                                            'code' => $template->code,
                                            'description' => $template->description,
                                            'subject_id' => $template->subject_id,
                                            'academic_class_id' => $template->academic_class_id,
                                            'academic_year_id' => $template->academic_year_id,
                                            'academic_term_id' => $template->academic_term_id,
                                            'is_default' => $template->is_default ? 1 : 0,
                                            'status' => $template->status,
                                            'items' => $template->items->map(fn($item) => [
                                                'id' => $item->id,
                                                'label' => $item->label,
                                                'code' => $item->code,
                                                'component_type' => $item->component_type,
                                                'max_score' => (float)$item->max_score,
                                                'position' => $item->position,
                                                'affects_total' => (bool)$item->affects_total,
                                            ])->values()->all(),
                                        ];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-primary-light">{{ $template->name }}</div>
                                            <div class="text-secondary-light text-xs">{{ $template->code }}</div>
                                        </td>
                                        <td>{{ $template->subject?->name ?: 'All subjects' }}</td>
                                        <td>{{ $template->academicClass?->name ?: 'All classes' }}</td>
                                        <td>{{ $template->items->count() }} items</td>
                                        <td>
                                            <span class="badge {{ $template->status === 'active' ? 'bg-primary-600' : 'bg-secondary' }}">
                                                {{ ucfirst($template->status ?: 'draft') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center gap-8">
                                                <button type="button" class="btn btn-sm btn-outline-primary-600 js-gb-template-edit" data-template='@json($templatePayload)'>Edit</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger-600 js-gb-template-delete" data-id="{{ $template->id }}" data-name="{{ $template->name }}">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals (Condensed) -->
    <!-- Academic Year Modal -->
    <div class="modal fade" id="academicYearModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="academicYearModalTitle">Add Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="academicYearForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.academic_years.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.academic_years.update', ['academic_year' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="2025/2026" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" placeholder="2025-2026" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicYearForm" class="btn btn-primary-600" id="academicYearSubmitButton">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Term Modal -->
    <div class="modal fade" id="academicTermModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="academicTermModalTitle">Add Term</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="academicTermForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.terms.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.terms.update', ['term' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year_id">
                                <option value="">Select Year</option>
                                @foreach ($academicYears as $y)
                                    <option value="{{ $y->id }}">{{ $y->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" />
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicTermForm" class="btn btn-primary-600" id="academicTermSubmitButton">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Scale Modal -->
    <div class="modal fade" id="gradeScaleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeScaleModalTitle">Add Grade Scale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="gradeScaleForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.grade_scales.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.grade_scales.update', ['grade_scale' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Min Score</label>
                            <input type="number" step="0.01" class="form-control" name="min_score" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Score</label>
                            <input type="number" step="0.01" class="form-control" name="max_score" />
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Default Scale</label>
                            <select class="form-select" name="is_default">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="gradeScaleForm" class="btn btn-primary-600" id="gradeScaleSubmitButton">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grading Template Modal -->
    <div class="modal fade right-drawer-modal drawer-lg" id="gradebookTemplateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="gradebookTemplateModalTitle">Create Assessment Template</h5>
                        <p class="mb-0 text-sm text-secondary-light">Define the workbook structure for a class or subject.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="gradebookTemplateForm"
                    data-create-url="{{ route('v1.gradebook.templates.store', [], false) }}"
                    data-update-url-template="{{ route('v1.gradebook.templates.update', ['template' => '__ID__'], false) }}">
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="gradebookTemplateModalError"></div>
                        <input type="hidden" name="id" />

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Template Name</label>
                                <input type="text" class="form-control" name="name" placeholder="Primary CA + Exam Template" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code</label>
                                <input type="text" class="form-control" name="code" placeholder="PRY-CA-EXAM" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <select class="form-select" name="subject_id">
                                    <option value="">All subjects</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class</label>
                                <select class="form-select" name="academic_class_id">
                                    <option value="">All classes</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Academic Year</label>
                                <select class="form-select js-template-year-select" name="academic_year_id">
                                    <option value="">All years</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Academic Term</label>
                                <select class="form-select js-template-term-select" name="academic_term_id">
                                    <option value="">All terms</option>
                                    @foreach ($terms as $term)
                                        <option value="{{ $term->id }}" data-academic-year-id="{{ $term->academic_year_id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="draft">Draft</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="gradebookTemplateIsDefault">
                                    <label class="form-check-label" for="gradebookTemplateIsDefault">Default for scope</label>
                                </div>
                            </div>
                        </div>

                        <div class="border-top border-neutral-200 mt-24 pt-24">
                            <div class="d-flex align-items-center justify-content-between gap-12 mb-16">
                                <div>
                                    <h6 class="mb-4 text-md">Template Items</h6>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary-600" id="gradebookAddTemplateItem">
                                    <i class="ri-add-line me-4"></i>Add Item
                                </button>
                            </div>
                            <div id="gradebookTemplateItems" class="d-flex flex-column gap-12"></div>
                            <div class="rounded-12 border border-dashed border-neutral-300 px-16 py-20 text-center text-secondary-light text-sm" id="gradebookTemplateItemsEmpty">
                                No items yet.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-600" id="gradebookTemplateSubmitButton">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template for items -->
    <template id="gradebookTemplateItemTemplate">
        <div class="rounded-12 border border-neutral-200 p-16 js-template-item-card">
            <div class="d-flex align-items-center justify-content-between gap-12 mb-12">
                <div class="fw-semibold text-primary-light">Item</div>
                <button type="button" class="btn btn-sm btn-outline-danger-600 js-remove-template-item">
                    <i class="ri-delete-bin-line"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Label</label>
                    <input type="text" class="form-control js-item-label" placeholder="Assignment 1" required />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Code</label>
                    <input type="text" class="form-control js-item-code" placeholder="A1" required />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select js-item-component-type">
                        <option value="continuous_assessment">CA</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Max</label>
                    <input type="number" step="0.01" class="form-control js-item-max-score" value="10" required />
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check mt-8">
                        <input class="form-check-input js-item-affects-total" type="checkbox" value="1" checked />
                        <label class="form-check-label">Affects Total</label>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Delete Confirmation Modal (Generic) -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmationMessage">Are you sure you want to delete this item?</p>
                    <form id="genericDeleteForm"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="genericDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            // Tab-specific action buttons
            const tabActions = {
                'years': '<button type="button" class="btn btn-primary-600 js-academic-year-modal-trigger" data-mode="create"><i class="ri-add-line me-4"></i>Add Session</button>',
                'terms': '<button type="button" class="btn btn-primary-600 js-academic-term-modal-trigger" data-mode="create"><i class="ri-add-line me-4"></i>Add Term</button>',
                'scales': '<button type="button" class="btn btn-primary-600 js-grade-scale-modal-trigger" data-mode="create"><i class="ri-add-line me-4"></i>Add Scale</button>',
                'templates': '<button type="button" class="btn btn-primary-600" id="openGradebookTemplateCreateModal"><i class="ri-add-line me-4"></i>Add Template</button>'
            };

            function updateActions(tabId) {
                $('#activeTabActions').html(tabActions[tabId] || '');
            }

            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                updateActions(e.target.id.replace('-tab', ''));
            });

            // Initial actions
            updateActions('years');

            // --- Academic Year Logic ---
            const yearForm = $('#academicYearForm');
            const yearModal = new bootstrap.Modal('#academicYearModal');
            
            $(document).on('click', '.js-academic-year-modal-trigger', function() {
                const mode = $(this).data('mode');
                const year = $(this).data('year');
                yearForm.trigger('reset');
                if (mode === 'edit' && year) {
                    yearForm.data('editingId', year.id);
                    yearForm.find('[name="name"]').val(year.name);
                    yearForm.find('[name="slug"]').val(year.slug);
                    yearForm.find('[name="start_date"]').val(year.start_date);
                    yearForm.find('[name="end_date"]').val(year.end_date);
                    yearForm.find('[name="status"]').val(year.status);
                    yearForm.find('[name="description"]').val(year.description);
                    $('#academicYearModalTitle').text('Edit Session');
                } else {
                    yearForm.removeData('editingId');
                    $('#academicYearModalTitle').text('Add Session');
                }
                yearModal.show();
            });

            bindAcademicAjaxForm({
                formSelector: '#academicYearForm',
                url: (f) => f.data('editingId') ? f.data('updateUrlTemplate').replace('__ID__', f.data('editingId')) : f.data('createUrl'),
                method: (f) => f.data('editingId') ? 'PUT' : 'POST',
                reloadOnSuccess: true,
                onSuccess: () => yearModal.hide()
            });

            // --- Academic Term Logic ---
            const termForm = $('#academicTermForm');
            const termModal = new bootstrap.Modal('#academicTermModal');

            $(document).on('click', '.js-academic-term-modal-trigger', function() {
                const mode = $(this).data('mode');
                const term = $(this).data('term');
                termForm.trigger('reset');
                if (mode === 'edit' && term) {
                    termForm.data('editingId', term.id);
                    termForm.find('[name="name"]').val(term.name);
                    termForm.find('[name="academic_year_id"]').val(term.academic_year_id);
                    termForm.find('[name="slug"]').val(term.slug);
                    termForm.find('[name="start_date"]').val(term.start_date);
                    termForm.find('[name="end_date"]').val(term.end_date);
                    termForm.find('[name="status"]').val(term.status);
                    termForm.find('[name="description"]').val(term.description);
                    $('#academicTermModalTitle').text('Edit Term');
                } else {
                    termForm.removeData('editingId');
                    $('#academicTermModalTitle').text('Add Term');
                }
                termModal.show();
            });

            bindAcademicAjaxForm({
                formSelector: '#academicTermForm',
                url: (f) => f.data('editingId') ? f.data('updateUrlTemplate').replace('__ID__', f.data('editingId')) : f.data('createUrl'),
                method: (f) => f.data('editingId') ? 'PUT' : 'POST',
                reloadOnSuccess: true,
                onSuccess: () => termModal.hide()
            });

            // --- Grade Scale Logic ---
            const scaleForm = $('#gradeScaleForm');
            const scaleModal = new bootstrap.Modal('#gradeScaleModal');

            $(document).on('click', '.js-grade-scale-modal-trigger', function() {
                const mode = $(this).data('mode');
                const scale = $(this).data('gradeScale');
                scaleForm.trigger('reset');
                if (mode === 'edit' && scale) {
                    scaleForm.data('editingId', scale.id);
                    scaleForm.find('[name="name"]').val(scale.name);
                    scaleForm.find('[name="code"]').val(scale.code);
                    scaleForm.find('[name="min_score"]').val(scale.min_score);
                    scaleForm.find('[name="max_score"]').val(scale.max_score);
                    scaleForm.find('[name="is_default"]').val(scale.is_default);
                    scaleForm.find('[name="description"]').val(scale.description);
                    $('#gradeScaleModalTitle').text('Edit Scale');
                } else {
                    scaleForm.removeData('editingId');
                    $('#gradeScaleModalTitle').text('Add Scale');
                }
                scaleModal.show();
            });

            bindAcademicAjaxForm({
                formSelector: '#gradeScaleForm',
                url: (f) => f.data('editingId') ? f.data('updateUrlTemplate').replace('__ID__', f.data('editingId')) : f.data('createUrl'),
                method: (f) => f.data('editingId') ? 'PUT' : 'POST',
                reloadOnSuccess: true,
                onSuccess: () => scaleModal.hide()
            });

            // --- Deletion Logic ---
            const deleteModal = new bootstrap.Modal('#deleteConfirmationModal');
            const deleteForm = $('#genericDeleteForm');

            $(document).on('click', '.js-academic-year-delete-trigger, .js-academic-term-delete-trigger, .js-grade-scale-delete-trigger, .js-gb-template-delete', function() {
                let url = "";
                let name = "";
                
                if ($(this).hasClass('js-academic-year-delete-trigger')) {
                    const data = $(this).data('year');
                    url = `{{ url('api/v1/academics/academic-years') }}/${data.id}`;
                    name = data.name;
                } else if ($(this).hasClass('js-academic-term-delete-trigger')) {
                    const data = $(this).data('term');
                    url = `{{ url('api/v1/academics/terms') }}/${data.id}`;
                    name = data.name;
                } else if ($(this).hasClass('js-grade-scale-delete-trigger')) {
                    const data = $(this).data('gradeScale');
                    url = `{{ url('api/v1/academics/grade-scales') }}/${data.id}`;
                    name = data.name;
                } else if ($(this).hasClass('js-gb-template-delete')) {
                    const id = $(this).data('id');
                    url = `{{ url('api/v1/academics/gradebook') }}/${id}`;
                    name = $(this).data('name');
                }
                
                deleteForm.data('url', url);
                $('#deleteConfirmationMessage').text(`Are you sure you want to delete "${name}"? This action is irreversible.`);
                deleteModal.show();
            });

            bindAcademicAjaxForm({
                formSelector: '#genericDeleteForm',
                url: (f) => f.data('url'),
                method: 'DELETE',
                reloadOnSuccess: true,
                onSuccess: () => deleteModal.hide()
            });

            // --- Grading Template Logic ---
            const templateModalEl = document.getElementById('gradebookTemplateModal');
            const templateModal = new bootstrap.Modal(templateModalEl);
            const templateForm = document.getElementById('gradebookTemplateForm');
            const templateItemsContainer = document.getElementById('gradebookTemplateItems');
            const templateItemsEmpty = document.getElementById('gradebookTemplateItemsEmpty');
            const templateItemTpl = document.getElementById('gradebookTemplateItemTemplate');
            const templateYearSelect = templateForm.querySelector('.js-template-year-select');
            const templateTermSelect = templateForm.querySelector('.js-template-term-select');

            const filterTerms = (yearId) => {
                Array.from(templateTermSelect.options).forEach((opt, idx) => {
                    if (idx === 0) return;
                    opt.hidden = yearId && opt.dataset.academicYearId && opt.dataset.academicYearId !== yearId;
                });
                if (templateTermSelect.selectedOptions[0]?.hidden) templateTermSelect.value = "";
            };

            const appendTemplateItem = (item = {}) => {
                const fragment = templateItemTpl.content.cloneNode(true);
                const card = fragment.querySelector('.js-template-item-card');
                card.querySelector('.js-item-label').value = item.label || '';
                card.querySelector('.js-item-code').value = item.code || '';
                card.querySelector('.js-item-component-type').value = item.component_type || 'continuous_assessment';
                card.querySelector('.js-item-max-score').value = item.max_score || 10;
                card.querySelector('.js-item-affects-total').checked = item.affects_total !== false;
                templateItemsContainer.appendChild(fragment);
                templateItemsEmpty.classList.add('d-none');
            };

            const collectTemplateItems = () => {
                return Array.from(templateItemsContainer.querySelectorAll('.js-template-item-card')).map((card, idx) => ({
                    label: card.querySelector('.js-item-label').value,
                    code: card.querySelector('.js-item-code').value,
                    component_type: card.querySelector('.js-item-component-type').value,
                    max_score: card.querySelector('.js-item-max-score').value,
                    position: idx + 1,
                    affects_total: card.querySelector('.js-item-affects-total').checked
                }));
            };

            $(document).on('click', '#openGradebookTemplateCreateModal', function() {
                templateForm.reset();
                templateForm.removeAttribute('data-editing-id');
                templateItemsContainer.innerHTML = '';
                templateItemsEmpty.classList.remove('d-none');
                $('#gradebookTemplateModalTitle').text('Create Assessment Template');
                templateModal.show();
            });

            $(document).on('click', '#gradebookAddTemplateItem', () => appendTemplateItem());
            $(document).on('click', '.js-remove-template-item', function() {
                $(this).closest('.js-template-item-card').remove();
                if (templateItemsContainer.children.length === 0) templateItemsEmpty.classList.remove('d-none');
            });

            $(document).on('click', '.js-gb-template-edit', function() {
                const data = $(this).data('template');
                templateForm.reset();
                templateForm.setAttribute('data-editing-id', data.id);
                templateForm.querySelector('[name="name"]').value = data.name;
                templateForm.querySelector('[name="code"]').value = data.code || '';
                templateForm.querySelector('[name="description"]').value = data.description || '';
                templateForm.querySelector('[name="subject_id"]').value = data.subject_id || '';
                templateForm.querySelector('[name="academic_class_id"]').value = data.academic_class_id || '';
                templateForm.querySelector('[name="academic_year_id"]').value = data.academic_year_id || '';
                filterTerms(data.academic_year_id);
                templateForm.querySelector('[name="academic_term_id"]').value = data.academic_term_id || '';
                templateForm.querySelector('[name="status"]').value = data.status || 'active';
                templateForm.querySelector('[name="is_default"]').checked = Boolean(data.is_default);
                templateItemsContainer.innerHTML = '';
                (data.items || []).forEach(item => appendTemplateItem(item));
                $('#gradebookTemplateModalTitle').text(`Edit ${data.name}`);
                templateModal.show();
            });

            $(templateYearSelect).on('change', function() { filterTerms(this.value); });

            $('#gradebookTemplateForm').on('submit', function(e) {
                e.preventDefault();
                const editingId = this.getAttribute('data-editing-id');
                const url = editingId ? this.dataset.updateUrlTemplate.replace('__ID__', editingId) : this.dataset.createUrl;
                const method = editingId ? 'PUT' : 'POST';
                const payload = {
                    name: this.querySelector('[name="name"]').value,
                    code: this.querySelector('[name="code"]').value,
                    description: this.querySelector('[name="description"]').value,
                    subject_id: this.querySelector('[name="subject_id"]').value || null,
                    academic_class_id: this.querySelector('[name="academic_class_id"]').value || null,
                    academic_year_id: this.querySelector('[name="academic_year_id"]').value || null,
                    academic_term_id: this.querySelector('[name="academic_term_id"]').value || null,
                    status: this.querySelector('[name="status"]').value,
                    is_default: this.querySelector('[name="is_default"]').checked,
                    items: collectTemplateItems()
                };

                Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url, method, data: JSON.stringify(payload), contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                }).done(() => {
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Template saved.' }).then(() => window.location.reload());
                }).fail((xhr) => {
                    Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to save template.' });
                });
            });

        })(window.jQuery);
    </script>
@endpush
