@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Grade Appeals</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <span class="text-secondary-light">/ Appeals</span>
            </div>
        </div>
        <button type="button" class="btn btn-primary-600 d-flex align-items-center gap-6 js-appeal-create-trigger">
            <span class="d-flex text-md"><i class="ri-add-large-line"></i></span>
            Submit Appeal
        </button>
    </div>

    <div class="card h-100">
        <div class="card-body p-0 dataTable-wrapper">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-16 px-20 py-12 border-bottom border-neutral-200">
                <div>
                    <h6 class="mb-4">Appeal Queue</h6>
                    <p class="mb-0 text-secondary-light">Submit, review, and clear appeals from one live list.</p>
                </div>
                <form class="navbar-search dt-search m-0">
                    <input type="text" class="dt-input bg-transparent radius-4" name="search" placeholder="Search appeals..." />
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            <div class="p-0">
                <table class="table bordered-table mb-0 data-table" id="appealsTable" data-page-length="10">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Student</th>
                            <th>Result</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Resolved</th>
                            <th>Reason</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($appeals ?? collect()) as $appeal)
                            @php
                                $studentName = $appeal->student?->full_name ?? trim(($appeal->student?->first_name ?? '').' '.($appeal->student?->last_name ?? ''));
                                $resultLabel = trim(
                                    implode(' - ', array_filter([
                                        $appeal->result?->student?->full_name ?? trim(($appeal->result?->student?->first_name ?? '').' '.($appeal->result?->student?->last_name ?? '')),
                                        $appeal->result?->academicClass?->name,
                                        $appeal->result?->academic_session,
                                        $appeal->result?->term,
                                    ])),
                                );
                                $appealPayload = [
                                    'id' => $appeal->id,
                                    'result_id' => $appeal->result_id,
                                    'student_id' => $appeal->student_id,
                                    'student_name' => $studentName,
                                    'result_label' => $resultLabel,
                                    'reason' => $appeal->reason,
                                    'status' => $appeal->status?->value ?? $appeal->status,
                                    'resolution' => $appeal->resolution,
                                ];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-semibold text-primary-light">{{ $studentName ?: '—' }}</td>
                                <td>{{ $resultLabel ?: '—' }}</td>
                                <td>{{ $appeal->status?->label() ?? $appeal->status ?? '—' }}</td>
                                <td>{{ $appeal?->submitted_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td>{{ $appeal?->resolved_at?->format('d M, Y h:i A') ?? '—' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($appeal->reason, 50) }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-8">
                                        <button type="button" class="btn btn-sm btn-outline-primary-600 js-appeal-resolve-trigger"
                                            data-appeal='@json($appealPayload)'>Review</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger-600 js-appeal-delete-trigger"
                                            data-appeal='@json($appealPayload)'>Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-24 text-secondary-light">No appeals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="appealCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Submit Appeal</h5>
                        <p class="mb-0 text-sm text-secondary-light">Select the result, add your note, and submit.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appealCreateForm" class="row g-3"
                        data-create-url="{{ route('v1.academics.appeals.store', [], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Result</label>
                            <select class="form-select" name="result_id">
                                <option value="">Select Result</option>
                                @foreach (($results ?? collect()) as $result)
                                    <option value="{{ $result->id }}">
                                        {{ ($result->student?->full_name ?? $result->student_id ?? 'Student') }} - {{ $result->academicClass?->name ?? 'Class' }} - {{ $result->academic_session ?: 'Session' }} - {{ $result->term ?: 'Term' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id">
                                <option value="">Select Student</option>
                                @foreach (($students ?? collect()) as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name ?? trim(($student->first_name ?? '').' '.($student->last_name ?? '')) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control" rows="4" name="reason"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="appealCreateForm" class="btn btn-primary-600">Submit Appeal</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="appealResolveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="appealResolveTitle">Review Appeal</h5>
                        <p class="mb-0 text-sm text-secondary-light">Update the appeal status and resolution notes.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="appealResolveForm" class="row g-3"
                        data-update-url-template="{{ route('v1.academics.appeals.resolve', ['id' => '__ID__'], false) }}">
                        <div class="col-md-6">
                            <label class="form-label">Student</label>
                            <input type="text" class="form-control" id="appealResolveStudent" readonly />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Result</label>
                            <input type="text" class="form-control" id="appealResolveResult" readonly />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control" id="appealResolveReason" rows="3" readonly></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                @foreach (($statuses ?? ['submitted', 'under_review', 'resolved', 'rejected']) as $status)
                                    @if ($status !== 'submitted')
                                        <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Resolution</label>
                            <textarea class="form-control" name="resolution" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="appealResolveForm" class="btn btn-primary-600" id="appealResolveSubmitButton">Update Appeal</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="appealDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Appeal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-8">Are you sure you want to delete <strong id="appealDeleteName">this appeal</strong>?</p>
                    <p class="mb-0 text-sm text-secondary-light">This action cannot be undone.</p>
                    <form id="appealDeleteForm" data-delete-url-template="{{ route('v1.academics.appeals.destroy', ['appeal' => '__ID__'], false) }}"></form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-neutral" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="appealDeleteForm" class="btn btn-danger-600">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('illimi-academics::partials.ajax-form-handler')
    <script>
        (function($) {
            const createForm = $('#appealCreateForm');
            const resolveForm = $('#appealResolveForm');
            const deleteForm = $('#appealDeleteForm');
            const createModal = new bootstrap.Modal(document.getElementById('appealCreateModal'));
            const resolveModal = new bootstrap.Modal(document.getElementById('appealResolveModal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('appealDeleteModal'));

            const resetCreateForm = () => createForm.trigger('reset');

            const resetResolveForm = () => {
                resolveForm.trigger('reset');
                resolveForm.removeData('editingId');
                $('#appealResolveStudent').val('');
                $('#appealResolveResult').val('');
                $('#appealResolveReason').val('');
                resolveForm.find('[name="status"]').val('under_review');
            };

            $(document).on('click', '.js-appeal-create-trigger', function() {
                resetCreateForm();
                createModal.show();
            });

            $(document).on('click', '.js-appeal-resolve-trigger', function() {
                const payload = $(this).data('appeal');
                resetResolveForm();
                resolveForm.data('editingId', payload.id);
                $('#appealResolveStudent').val(payload.student_name || '—');
                $('#appealResolveResult').val(payload.result_label || '—');
                $('#appealResolveReason').val(payload.reason || '');
                resolveForm.find('[name="status"]').val(payload.status === 'submitted' ? 'under_review' : (payload.status || 'under_review'));
                resolveForm.find('[name="resolution"]').val(payload.resolution || '');
                resolveModal.show();
            });

            $(document).on('click', '.js-appeal-delete-trigger', function() {
                const payload = $(this).data('appeal');
                deleteForm.data('deletingId', payload.id);
                $('#appealDeleteName').text(payload.student_name || 'this appeal');
                deleteModal.show();
            });

            $('#appealCreateModal').on('hidden.bs.modal', resetCreateForm);
            $('#appealResolveModal').on('hidden.bs.modal', resetResolveForm);
            $('#appealDeleteModal').on('hidden.bs.modal', function() {
                deleteForm.removeData('deletingId');
                $('#appealDeleteName').text('this appeal');
            });

            bindAcademicAjaxForm({
                formSelector: '#appealCreateForm',
                url: (currentForm) => currentForm.data('createUrl'),
                method: 'POST',
                loadingText: 'Submitting appeal...',
                successTitle: 'Appeal submitted',
                onSuccess: () => {
                    createModal.hide();
                    resetCreateForm();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#appealResolveForm',
                url: (currentForm) => currentForm.data('updateUrlTemplate').replace('__ID__', currentForm.data('editingId')),
                method: 'PUT',
                loadingText: 'Updating appeal...',
                successTitle: 'Appeal updated',
                onSuccess: () => {
                    resolveModal.hide();
                    resetResolveForm();
                }
            });

            bindAcademicAjaxForm({
                formSelector: '#appealDeleteForm',
                url: (currentForm) => currentForm.data('deleteUrlTemplate').replace('__ID__', currentForm.data('deletingId')),
                method: 'DELETE',
                loadingText: 'Deleting appeal...',
                successTitle: 'Appeal deleted',
                onSuccess: () => deleteModal.hide()
            });
        })(window.jQuery);
    </script>
@endpush
