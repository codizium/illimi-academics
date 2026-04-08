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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Academic Years</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Academic Years</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-academic-year-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md">
                <i class="ri-add-large-line"></i>
            </span>
            Add Academic Year
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Academic Year Directory</h6>
                    <p class="mb-0 text-secondary-light">Create, update, and retire sessions from one place.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search years..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="academicYearsTable" data-page-length="10">
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
                        @foreach (($academicYears ?? collect()) as $year)
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
                            <tr data-row-id="{{ $year->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-primary-light">{{ $year->name }}</div>
                                    <div class="text-sm text-secondary-light">{{ $year->slug ?: 'No slug provided' }}</div>
                                </td>
                                <td>
                                    <div>{{ $year?->start_date?->format('d M, Y') ?? 'N/A' }}</div>
                                    <div class="text-sm text-secondary-light">
                                        to {{ $year?->end_date?->format('d M, Y') ?? 'Open ended' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge text-sm fw-medium px-12 py-4 radius-8 bg-primary-50 text-primary-600">
                                        {{ $year->terms_count }} {{ \Illuminate\Support\Str::plural('term', $year->terms_count) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $statusClasses[$year->status] ?? 'bg-neutral-100 text-neutral-600' }}">
                                        {{ ucfirst($year->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-academic-year-modal-trigger"
                                            data-mode="edit" data-year='@json($yearPayload)'>
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-academic-year-delete-trigger"
                                            data-year='@json($yearPayload)'>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="academicYearModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="academicYearModalTitle">Add Academic Year</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="academicYearModalSubtitle">Fill in the session details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="academicYearForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.academic_years.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.academic_years.update', ['id' => '__ID__'], false) }}">
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
                                @foreach (($statuses ?? ['active', 'inactive', 'closed']) as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Optional notes about this academic year."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicYearForm" class="btn btn-primary-600" id="academicYearSubmitButton">Save Academic Year</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="academicYearDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Academic Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="academicYearDeleteName">this academic year</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="academicYearDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.academic_years.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicYearDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#academicYearForm');
            const deleteForm = $('#academicYearDeleteForm');
            const modalElement = document.getElementById('academicYearModal');
            const deleteModalElement = document.getElementById('academicYearDeleteModal');
            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
            const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
            const title = $('#academicYearModalTitle');
            const subtitle = $('#academicYearModalSubtitle');
            const submitButton = $('#academicYearSubmitButton');
            const nameInput = form.find('input[name="name"]');
            const slugInput = form.find('input[name="slug"]');

            let slugTouched = false;

            const slugify = (value) => value
                .toString()
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                title.text('Add Academic Year');
                subtitle.text('Fill in the session details and save.');
                submitButton.text('Save Academic Year');
                form.find('select[name="status"]').val('active');
                slugTouched = false;
            };

            const setEditMode = (year) => {
                resetCreateMode();
                form.data('editingId', year.id);
                form.data('method', 'PUT');
                title.text(`Edit ${year.name}`);
                subtitle.text('Update the academic year details and save your changes.');
                submitButton.text('Update Academic Year');

                form.find('input[name="name"]').val(year.name || '');
                form.find('input[name="slug"]').val(year.slug || '');
                form.find('input[name="start_date"]').val(year.start_date || '');
                form.find('input[name="end_date"]').val(year.end_date || '');
                form.find('select[name="status"]').val(year.status || 'active');
                form.find('textarea[name="description"]').val(year.description || '');
                slugTouched = Boolean(year.slug);
            };

            $(document).on('click', '.js-academic-year-modal-trigger', function() {
                const mode = $(this).data('mode');
                const year = $(this).data('year');

                if (mode === 'edit' && year) {
                    setEditMode(year);
                } else {
                    resetCreateMode();
                }

                modal?.show();
            });

            $(document).on('click', '.js-academic-year-delete-trigger', function() {
                const year = $(this).data('year');
                deleteForm.data('deletingId', year.id);
                $('#academicYearDeleteName').text(year.name || 'this academic year');
                deleteModal?.show();
            });

            $(modalElement).on('hidden.bs.modal', function() {
                resetCreateMode();
            });

            $(deleteModalElement).on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#academicYearDeleteName').text('this academic year');
            });

            slugInput.on('input', function() {
                slugTouched = $(this).val().trim().length > 0;
            });

            nameInput.on('input', function() {
                if (!slugTouched) {
                    slugInput.val(slugify($(this).val()));
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#academicYearForm',
                url: (currentForm) => {
                    const editingId = currentForm.data('editingId');

                    if (!editingId) {
                        return currentForm.data('createUrl');
                    }

                    return currentForm.data('updateUrlTemplate').replace('__ID__', editingId);
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving academic year...',
                successTitle: 'Academic year saved',
                onSuccess: () => {
                    modal?.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#academicYearDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting academic year...',
                successTitle: 'Academic year deleted',
                onSuccess: () => {
                    deleteModal?.hide();
                }
            });
        })(window.jQuery);
    </script>
@endpush
