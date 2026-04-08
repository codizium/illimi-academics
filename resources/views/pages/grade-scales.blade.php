@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Grade Scales</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Grade Scales</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-grade-scale-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Grade Scale
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Grade Scale Directory</h6>
                    <p class="mb-0 text-secondary-light">Create, update, and remove grading schemes without leaving the list.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search grade scales..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="gradeScalesTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Score Range</th>
                            <th>Default</th>
                            <th>Description</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (($gradeScales ?? collect()) as $gradeScale)
                            @php
                                $gradeScalePayload = [
                                    'id' => $gradeScale->id,
                                    'name' => $gradeScale->name,
                                    'code' => $gradeScale->code,
                                    'min_score' => $gradeScale->min_score !== null ? (float) $gradeScale->min_score : null,
                                    'max_score' => $gradeScale->max_score !== null ? (float) $gradeScale->max_score : null,
                                    'description' => $gradeScale->description,
                                    'is_default' => (int) $gradeScale->is_default,
                                ];
                            @endphp
                            <tr data-row-id="{{ $gradeScale->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $gradeScale->name }}</td>
                                <td>{{ $gradeScale->code ?: '—' }}</td>
                                <td>{{ $gradeScale->min_score !== null && $gradeScale->max_score !== null ? rtrim(rtrim(number_format((float) $gradeScale->min_score, 2, '.', ''), '0'), '.') . ' - ' . rtrim(rtrim(number_format((float) $gradeScale->max_score, 2, '.', ''), '0'), '.') : '—' }}</td>
                                <td>
                                    <span
                                        class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $gradeScale->is_default ? 'bg-success-focus text-success-main' : 'bg-neutral-100 text-neutral-600' }}">
                                        {{ $gradeScale->is_default ? 'Default' : 'Secondary' }}
                                    </span>
                                </td>
                                <td>{{ $gradeScale->description ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-grade-scale-modal-trigger"
                                            data-mode="edit" data-grade-scale='@json($gradeScalePayload)'>
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-grade-scale-delete-trigger"
                                            data-grade-scale='@json($gradeScalePayload)'>
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

    <div class="modal fade" id="gradeScaleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="gradeScaleModalTitle">Add Grade Scale</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="gradeScaleModalSubtitle">Fill in the grade scale details and save.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="gradeScaleForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.grade_scales.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.grade_scales.update', ['id' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Standard Scale" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" class="form-control" name="code" placeholder="STD" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimum Score</label>
                            <input type="number" class="form-control" name="min_score" min="0" step="0.01" placeholder="70" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Maximum Score</label>
                            <input type="number" class="form-control" name="max_score" min="0" step="0.01" placeholder="100" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default</label>
                            <select class="form-select" name="is_default">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Optional notes about this grade scale."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="gradeScaleForm" class="btn btn-primary-600" id="gradeScaleSubmitButton">Save Grade Scale</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gradeScaleDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Grade Scale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="gradeScaleDeleteName">this grade scale</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="gradeScaleDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.grade_scales.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="gradeScaleDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const form = $('#gradeScaleForm');
            const deleteForm = $('#gradeScaleDeleteForm');
            const modalElement = document.getElementById('gradeScaleModal');
            const deleteModalElement = document.getElementById('gradeScaleDeleteModal');
            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
            const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;
            const title = $('#gradeScaleModalTitle');
            const subtitle = $('#gradeScaleModalSubtitle');
            const submitButton = $('#gradeScaleSubmitButton');

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                form.data('method', 'POST');
                title.text('Add Grade Scale');
                subtitle.text('Fill in the grade scale details and save.');
                submitButton.text('Save Grade Scale');
                form.find('select[name="is_default"]').val('0');
            };

            const setEditMode = (gradeScale) => {
                resetCreateMode();
                form.data('editingId', gradeScale.id);
                form.data('method', 'PUT');
                title.text(`Edit ${gradeScale.name}`);
                subtitle.text('Update the grade scale details and save your changes.');
                submitButton.text('Update Grade Scale');

                form.find('input[name="name"]').val(gradeScale.name || '');
                form.find('input[name="code"]').val(gradeScale.code || '');
                form.find('input[name="min_score"]').val(gradeScale.min_score ?? '');
                form.find('input[name="max_score"]').val(gradeScale.max_score ?? '');
                form.find('select[name="is_default"]').val(String(gradeScale.is_default ?? 0));
                form.find('textarea[name="description"]').val(gradeScale.description || '');
            };

            $(document).on('click', '.js-grade-scale-modal-trigger', function() {
                const mode = $(this).data('mode');
                const gradeScale = $(this).data('gradeScale');

                if (mode === 'edit' && gradeScale) {
                    setEditMode(gradeScale);
                } else {
                    resetCreateMode();
                }

                modal?.show();
            });

            $(document).on('click', '.js-grade-scale-delete-trigger', function() {
                const gradeScale = $(this).data('gradeScale');
                deleteForm.data('deletingId', gradeScale.id);
                $('#gradeScaleDeleteName').text(gradeScale.name || 'this grade scale');
                deleteModal?.show();
            });

            $(modalElement).on('hidden.bs.modal', function() {
                resetCreateMode();
            });

            $(deleteModalElement).on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#gradeScaleDeleteName').text('this grade scale');
            });

            bindAcademicAjaxForm({
                formSelector: '#gradeScaleForm',
                url: (currentForm) => {
                    const editingId = currentForm.data('editingId');

                    if (!editingId) {
                        return currentForm.data('createUrl');
                    }

                    return currentForm.data('updateUrlTemplate').replace('__ID__', editingId);
                },
                method: (currentForm) => currentForm.data('method') || 'POST',
                loadingText: 'Saving grade scale...',
                successTitle: 'Grade scale saved',
                onSuccess: () => {
                    modal?.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#gradeScaleDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting grade scale...',
                successTitle: 'Grade scale deleted',
                onSuccess: () => {
                    deleteModal?.hide();
                }
            });
        })(window.jQuery);
    </script>
@endpush
