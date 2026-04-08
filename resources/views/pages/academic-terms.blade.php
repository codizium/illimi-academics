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
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Academic Terms</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Academic Terms</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-academic-term-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Term
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Academic Term Directory</h6>
                    <p class="mb-0 text-secondary-light">Manage each term window, status, and academic-year mapping.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search terms..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="academicTermsTable" data-page-length="10">
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
                        @forelse (($terms ?? collect()) as $term)
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
                                    <div class="text-sm text-secondary-light">{{ $term->slug ?: 'No slug provided' }}</div>
                                </td>
                                <td>{{ $term->academicYear?->name ?? '—' }}</td>
                                <td>
                                    <div>{{ $term?->start_date?->format('d M, Y') ?? 'N/A' }}</div>
                                    <div class="text-sm text-secondary-light">
                                        to {{ $term?->end_date?->format('d M, Y') ?? 'Open ended' }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $statusClasses[$term->status] ?? 'bg-neutral-100 text-neutral-600' }}">
                                        {{ ucfirst($term->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-academic-term-modal-trigger"
                                            data-mode="edit" data-term='@json($termPayload)'>
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-academic-term-delete-trigger"
                                            data-term='@json($termPayload)'>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-24 text-secondary-light">No terms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="academicTermModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="academicTermModalTitle">Add Term</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="academicTermModalSubtitle">Fill in the term details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="academicTermForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.terms.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.terms.update', ['id' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="First Term" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" name="academic_year_id">
                                <option value="">Select Academic Year</option>
                                @foreach (($academicYears ?? collect()) as $academicYear)
                                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" placeholder="first-term" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                @foreach (($statuses ?? ['active', 'inactive', 'closed']) as $status)
                                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Optional notes about this term."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicTermForm" class="btn btn-primary-600" id="academicTermSubmitButton">Save Term</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="academicTermDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Academic Term</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="academicTermDeleteName">this term</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="academicTermDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.terms.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicTermDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#academicTermForm');
            const deleteForm = $('#academicTermDeleteForm');
            const modalElement = document.getElementById('academicTermModal');
            const deleteModalElement = document.getElementById('academicTermDeleteModal');
            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
            const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
            const title = $('#academicTermModalTitle');
            const subtitle = $('#academicTermModalSubtitle');
            const submitButton = $('#academicTermSubmitButton');
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
                title.text('Add Term');
                subtitle.text('Fill in the term details and save.');
                submitButton.text('Save Term');
                form.find('select[name="status"]').val('active');
                slugTouched = false;
            };

            const setEditMode = (term) => {
                resetCreateMode();
                form.data('editingId', term.id);
                form.data('method', 'PUT');
                title.text(`Edit ${term.name}`);
                subtitle.text('Update the term details and save your changes.');
                submitButton.text('Update Term');

                form.find('select[name="academic_year_id"]').val(term.academic_year_id || '');
                form.find('input[name="name"]').val(term.name || '');
                form.find('input[name="slug"]').val(term.slug || '');
                form.find('input[name="start_date"]').val(term.start_date || '');
                form.find('input[name="end_date"]').val(term.end_date || '');
                form.find('select[name="status"]').val(term.status || 'active');
                form.find('textarea[name="description"]').val(term.description || '');
                slugTouched = Boolean(term.slug);
            };

            $(document).on('click', '.js-academic-term-modal-trigger', function() {
                const mode = $(this).data('mode');
                const term = $(this).data('term');

                if (mode === 'edit' && term) {
                    setEditMode(term);
                } else {
                    resetCreateMode();
                }

                modal?.show();
            });

            $(document).on('click', '.js-academic-term-delete-trigger', function() {
                const term = $(this).data('term');
                deleteForm.data('deletingId', term.id);
                $('#academicTermDeleteName').text(term.name || 'this term');
                deleteModal?.show();
            });

            $(modalElement).on('hidden.bs.modal', function() {
                resetCreateMode();
            });

            $(deleteModalElement).on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#academicTermDeleteName').text('this term');
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
                formSelector: '#academicTermForm',
                url: (currentForm) => {
                    const editingId = currentForm.data('editingId');

                    if (!editingId) {
                        return currentForm.data('createUrl');
                    }

                    return currentForm.data('updateUrlTemplate').replace('__ID__', editingId);
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving term...',
                successTitle: 'Term saved',
                onSuccess: () => {
                    modal?.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#academicTermDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting term...',
                successTitle: 'Term deleted',
                onSuccess: () => {
                    deleteModal?.hide();
                }
            });
        })(window.jQuery);
    </script>
@endpush
