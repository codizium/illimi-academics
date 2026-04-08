@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <style>
        .syllabus-editor {
            min-height: 220px;
        }

        .syllabus-documents-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .syllabus-document-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            background: #f9fafb;
        }

        .syllabus-document-chip.is-removed {
            opacity: 0.55;
            text-decoration: line-through;
        }

        .syllabus-view-content {
            min-height: 140px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Syllabi</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Syllabi</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-syllabus-modal-trigger"
            data-mode="create">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Add Syllabus
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div
                class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Syllabus Directory</h6>
                    <p class="mb-0 text-secondary-light">Manage rich syllabus content and supporting documents from one place.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search"
                        placeholder="Search syllabi..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="syllabiTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Updated</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (($syllabi ?? collect()) as $syllabus)
                            @php
                                $syllabusPayload = [
                                    'id' => $syllabus->id,
                                    'subject_id' => $syllabus->subject_id,
                                    'subject_name' => $syllabus->subject?->name,
                                    'title' => $syllabus->title,
                                    'description' => $syllabus->description,
                                    'content' => $syllabus->content,
                                    'is_published' => (int) $syllabus->is_published,
                                    'documents' => $syllabus->attachments
                                        ->map(
                                            fn($attachment) => [
                                                'id' => $attachment->id,
                                                'label' => $attachment->label,
                                                'file_type' => $attachment->file_type,
                                                'file_url' => $attachment->file_url,
                                            ],
                                        )
                                        ->values()
                                        ->all(),
                                ];
                            @endphp
                            <tr data-row-id="{{ $syllabus->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $syllabus->title }}</td>
                                <td>{{ $syllabus->subject?->name ?? '—' }}</td>
                                <td>
                                    <span
                                        class="badge text-sm fw-medium px-12 py-4 radius-8 {{ $syllabus->is_published ? 'bg-success-focus text-success-main' : 'bg-warning-focus text-warning-main' }}">
                                        {{ $syllabus->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ $syllabus->attachments_count ?? $syllabus->attachments->count() }}</td>
                                <td>{{ $syllabus->updated_at?->format('d M, Y H:i') ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary js-syllabus-view-trigger"
                                            data-syllabus='@json($syllabusPayload)'>View</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary-600 js-syllabus-modal-trigger"
                                            data-mode="edit" data-syllabus='@json($syllabusPayload)'>Edit</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger-600 js-syllabus-delete-trigger"
                                            data-syllabus='@json($syllabusPayload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="syllabusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="syllabusModalTitle">Add Syllabus</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="syllabusModalSubtitle">Build a rich syllabus and attach supporting documents.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="syllabusForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.syllabi.store', [], false) }}"
                        data-update-url-template="{{ route('v1.academics.syllabi.update', ['id' => '__ID__'], false) }}">
                        <input type="hidden" name="content" />
                        <div class="col-md-6">
                            <label class="form-label">Subject</label>
                            <select class="form-select" name="subject_id">
                                <option value="">Select Subject</option>
                                @foreach (($subjects ?? collect()) as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" placeholder="Mathematics Scheme of Work" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="is_published">
                                <option value="0">Draft</option>
                                <option value="1">Published</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" placeholder="Optional short summary" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Syllabus Content</label>
                            <div id="syllabusEditorToolbar">
                                <span class="ql-formats">
                                    <select class="ql-header">
                                        <option selected></option>
                                        <option value="1"></option>
                                        <option value="2"></option>
                                    </select>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                    <button class="ql-blockquote"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-link"></button>
                                    <button class="ql-clean"></button>
                                </span>
                            </div>
                            <div id="syllabusEditor" class="syllabus-editor"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Documents</label>
                            <input type="file" class="form-control" name="documents[]" multiple />
                            <div class="form-text">Upload multiple supporting files such as PDFs, DOCX, slides, or images.</div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-8">
                                <label class="form-label mb-0">Attached Documents</label>
                                <span class="text-sm text-secondary-light">Existing files can be marked for removal before saving.</span>
                            </div>
                            <div id="syllabusExistingDocuments" class="syllabus-documents-list"></div>
                            <div id="syllabusSelectedDocuments" class="text-sm text-secondary-light mt-12"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="syllabusForm" class="btn btn-primary-600" id="syllabusSubmitButton">Save Syllabus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="syllabusViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="syllabusViewTitle">Syllabus Details</h5>
                        <p class="mb-0 text-sm text-secondary-light" id="syllabusViewSubtitle">Review the syllabus content and attached documents.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-secondary-light">Subject</label>
                            <div class="fw-semibold text-primary-light" id="syllabusViewSubject">—</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary-light">Status</label>
                            <div id="syllabusViewStatus">—</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary-light">Documents</label>
                            <div class="fw-semibold text-primary-light" id="syllabusViewDocumentCount">0</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary-light">Description</label>
                            <div class="text-primary-light" id="syllabusViewDescription">—</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary-light">Content</label>
                            <div class="syllabus-view-content" id="syllabusViewContent">No content added yet.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-secondary-light">Attached Documents</label>
                            <div id="syllabusViewDocuments" class="syllabus-documents-list"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="syllabusDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Syllabus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="syllabusDeleteName">this syllabus</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action will also remove attached documents.</p>
                    <form id="syllabusDeleteForm"
                        data-delete-url-template="{{ route('v1.academics.syllabi.destroy', ['id' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="syllabusDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>
        (function($) {
            const form = $('#syllabusForm');
            const deleteForm = $('#syllabusDeleteForm');
            const modal = new bootstrap.Modal(document.getElementById('syllabusModal'));
            const viewModal = new bootstrap.Modal(document.getElementById('syllabusViewModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('syllabusDeleteModal'));
            const existingDocumentsContainer = $('#syllabusExistingDocuments');
            const selectedDocumentsContainer = $('#syllabusSelectedDocuments');
            const viewDocumentsContainer = $('#syllabusViewDocuments');
            const documentsInput = form.find('[name="documents[]"]');
            const contentInput = form.find('[name="content"]');
            const removedAttachmentIds = new Set();
            const quill = typeof window.Quill === 'function'
                ? new window.Quill('#syllabusEditor', {
                    theme: 'snow',
                    modules: {
                        toolbar: '#syllabusEditorToolbar',
                    },
                })
                : null;

            const getEditorHtml = () => {
                if (!quill) {
                    return contentInput.val() || '';
                }

                const html = quill.root.innerHTML;
                return html === '<p><br></p>' ? '' : html;
            };

            const syncEditorInput = () => {
                contentInput.val(getEditorHtml());
            };

            const renderExistingDocuments = (documents) => {
                existingDocumentsContainer.empty();

                if (!documents || !documents.length) {
                    existingDocumentsContainer.html('<span class="text-sm text-secondary-light">No documents attached yet.</span>');
                    return;
                }

                documents.forEach((document) => {
                    const chip = $(`
                        <span class="syllabus-document-chip" data-attachment-id="${document.id}">
                            <a href="${document.file_url}" target="_blank" rel="noopener noreferrer">${document.label || 'Document'}</a>
                            <button type="button" class="btn btn-sm btn-outline-danger-600 js-syllabus-remove-attachment">Remove</button>
                        </span>
                    `);

                    existingDocumentsContainer.append(chip);
                });
            };

            const renderViewDocuments = (documents) => {
                viewDocumentsContainer.empty();

                if (!documents || !documents.length) {
                    viewDocumentsContainer.html('<span class="text-sm text-secondary-light">No documents attached.</span>');
                    return;
                }

                documents.forEach((document) => {
                    const chip = $(`
                        <span class="syllabus-document-chip">
                            <a href="${document.file_url}" target="_blank" rel="noopener noreferrer">${document.label || 'Document'}</a>
                            <span class="text-xs text-secondary-light">${document.file_type || ''}</span>
                        </span>
                    `);

                    viewDocumentsContainer.append(chip);
                });
            };

            const openViewModal = (payload) => {
                $('#syllabusViewTitle').text(payload.title || 'Syllabus Details');
                $('#syllabusViewSubtitle').text('Review the syllabus content and attached documents.');
                $('#syllabusViewSubject').text(payload.subject_name || '—');
                $('#syllabusViewStatus').html(
                    payload.is_published ?
                    '<span class="badge text-sm fw-medium px-12 py-4 radius-8 bg-success-focus text-success-main">Published</span>' :
                    '<span class="badge text-sm fw-medium px-12 py-4 radius-8 bg-warning-focus text-warning-main">Draft</span>'
                );
                $('#syllabusViewDocumentCount').text(String((payload.documents || []).length));
                $('#syllabusViewDescription').text(payload.description || '—');
                $('#syllabusViewContent').html(payload.content || '<span class="text-secondary-light">No content added yet.</span>');
                renderViewDocuments(payload.documents || []);
                viewModal.show();
            };

            const resetCreateMode = () => {
                form.trigger('reset');
                form.removeData('editingId');
                $('#syllabusModalTitle').text('Add Syllabus');
                $('#syllabusModalSubtitle').text('Build a rich syllabus and attach supporting documents.');
                $('#syllabusSubmitButton').text('Save Syllabus');
                removedAttachmentIds.clear();
                renderExistingDocuments([]);
                selectedDocumentsContainer.text('');
                contentInput.val('');

                if (quill) {
                    quill.setContents([]);
                }
            };

            const setEditMode = (payload) => {
                resetCreateMode();
                form.data('editingId', payload.id);
                $('#syllabusModalTitle').text(`Edit ${payload.title}`);
                $('#syllabusModalSubtitle').text('Update the syllabus, documents, and publishing state.');
                $('#syllabusSubmitButton').text('Update Syllabus');
                form.find('[name="subject_id"]').val(payload.subject_id || '');
                form.find('[name="title"]').val(payload.title || '');
                form.find('[name="description"]').val(payload.description || '');
                form.find('[name="is_published"]').val(String(payload.is_published ?? 0));
                contentInput.val(payload.content || '');

                if (quill) {
                    quill.clipboard.dangerouslyPasteHTML(payload.content || '');
                }

                renderExistingDocuments(payload.documents || []);
            };

            documentsInput.on('change', function() {
                const files = Array.from(this.files || []);
                selectedDocumentsContainer.text(
                    files.length ? `${files.length} file(s) selected: ${files.map((file) => file.name).join(', ')}` : ''
                );
            });

            if (quill) {
                quill.on('text-change', syncEditorInput);
            }

            $(document).on('click', '.js-syllabus-modal-trigger', function() {
                const mode = $(this).data('mode');
                const payload = $(this).data('syllabus');

                if (mode === 'edit' && payload) {
                    setEditMode(payload);
                } else {
                    resetCreateMode();
                }

                modal.show();
            });

            $(document).on('click', '.js-syllabus-delete-trigger', function() {
                const payload = $(this).data('syllabus');
                deleteForm.data('deletingId', payload.id);
                $('#syllabusDeleteName').text(payload.title || 'this syllabus');
                deleteModal.show();
            });

            $(document).on('click', '.js-syllabus-view-trigger', function() {
                const payload = $(this).data('syllabus');
                openViewModal(payload || {});
            });

            $(document).on('click', '.js-syllabus-remove-attachment', function() {
                const chip = $(this).closest('[data-attachment-id]');
                const attachmentId = String(chip.data('attachmentId') || '');

                if (!attachmentId) {
                    return;
                }

                if (removedAttachmentIds.has(attachmentId)) {
                    removedAttachmentIds.delete(attachmentId);
                    chip.removeClass('is-removed');
                    $(this).text('Remove');
                    return;
                }

                removedAttachmentIds.add(attachmentId);
                chip.addClass('is-removed');
                $(this).text('Undo');
            });

            $('#syllabusModal').on('hidden.bs.modal', resetCreateMode);
            $('#syllabusDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#syllabusDeleteName').text('this syllabus');
            });

            bindAcademicAjaxForm({
                formSelector: '#syllabusForm',
                url: (currentForm) => {
                    const editingId = currentForm.data('editingId');

                    if (!editingId) {
                        return currentForm.data('createUrl');
                    }

                    return currentForm.data('updateUrlTemplate').replace('__ID__', editingId);
                },
                method: 'POST',
                buildPayload: (currentForm) => {
                    syncEditorInput();

                    const formData = new FormData(currentForm[0]);
                    formData.set('content', getEditorHtml());

                    removedAttachmentIds.forEach((attachmentId) => {
                        formData.append('remove_attachment_ids[]', attachmentId);
                    });

                    if (currentForm.data('editingId')) {
                        formData.append('_method', 'PUT');
                    }

                    return formData;
                },
                loadingText: 'Saving syllabus...',
                successTitle: 'Syllabus saved',
                onSuccess: () => {
                    modal.hide();
                    resetCreateMode();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#syllabusDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting syllabus...',
                successTitle: 'Syllabus deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
