@extends('illimi-academics::frontend.layout')

@php
    $logoUrl = null;
    if ($organization && method_exists($organization, 'hasAttachment') && $organization->hasAttachment('logo')) {
        $logoUrl = $organization->getAttachmentUrl('logo');
    }

    $addressParts = array_filter([
        $organization?->address ?? null,
        $organization?->city ?? null,
        $organization?->state ?? null,
        $organization?->country ?? null,
    ]);

    $componentDefinitions = [];
    $componentKeys = [];

    foreach (($resultSlip['assessments'] ?? []) as $assessment) {
        foreach (($assessment['components'] ?? []) as $component) {
            $key = trim((string) ($component['code'] ?? $component['label'] ?? ''));
            if ($key === '' || isset($componentKeys[$key])) {
                continue;
            }

            $componentKeys[$key] = true;
            $componentDefinitions[] = [
                'key' => $key,
                'title' => ($component['code'] ?? null)
                    ? sprintf('%s (%s)', $component['code'], $component['label'] ?? $component['code'])
                    : ($component['label'] ?? $key),
            ];
        }
    }
@endphp

@section('title', 'Terminal Result')

@push('styles')
    <style>
        .display-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 24px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,250,252,0.96));
        }

        .display-sheet {
            position: relative;
            padding: 8px 10px 18px;
        }

        .display-paper {
            position: relative;
            background: rgba(255,255,255,0.94);
            padding: 10px 12px 18px;
            border: 1px solid #d7dee7;
            min-height: 100%;
        }

        .display-head {
            display: grid;
            grid-template-columns: 96px 1fr 110px;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
        }

        .display-head-logo {
            width: 88px;
            height: 88px;
            object-fit: contain;
        }

        .display-head-qr {
            width: 98px;
            height: 98px;
            border: 2px solid #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #111827;
            background: #fff;
            position: relative;
            overflow: hidden;
            padding: 6px;
        }

        .display-head-qr svg {
            width: 100%;
            height: 100%;
        }

        .display-head-qr span {
            position: absolute;
            inset: auto 0 0 0;
            background: rgba(255,255,255,0.92);
            text-align: center;
            padding: 4px 2px;
            font-size: .74rem;
        }

        .display-school {
            text-align: center;
        }

        .display-school h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .display-school p {
            margin: 4px 0 0;
            font-style: italic;
        }

        .display-school h2 {
            margin: 18px 0 0;
            font-size: 1.45rem;
            font-weight: 800;
        }

        .display-meta {
            margin-top: 10px;
        }

        .display-meta-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(0, .8fr);
            gap: 8px 28px;
        }

        .display-meta-item {
            display: flex;
            gap: 10px;
            align-items: baseline;
            border-bottom: 1px solid #2f3640;
            min-height: 28px;
            font-size: 1rem;
        }

        .display-meta-item strong {
            min-width: 74px;
        }

        .display-meta-item span {
            font-weight: 700;
            flex: 1;
            text-align: center;
        }

        .display-section-title {
            margin: 16px 0 10px;
            padding: 2px 0;
            text-align: center;
            font-size: 1.05rem;
            font-weight: 800;
            border-top: 2px solid #111827;
            border-bottom: 2px solid #111827;
        }

        .display-table {
            width: 100%;
            border-collapse: collapse;
        }

        .display-table th,
        .display-table td {
            border: 1px solid #cfd8e3;
            padding: 5px 4px;
            font-size: .88rem;
            text-align: center;
        }

        .display-table th {
            font-size: .76rem;
            font-weight: 800;
            text-transform: uppercase;
            background: #fff;
            border-top: none;
            border-bottom: 2px solid #111827;
        }

        .display-table td.subject-col {
            text-align: left;
            font-weight: 700;
        }

        .display-summary {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 14px;
            font-size: 1rem;
            font-weight: 800;
            border-top: 2px solid #111827;
            padding-top: 8px;
        }

        .display-watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.055;
            font-size: 18rem;
            font-weight: 800;
            color: var(--sheet-accent);
        }

        .display-assessment-grid {
            margin-top: 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 16px;
        }

        .display-mini-table {
            width: 100%;
            border-collapse: collapse;
        }

        .display-mini-table th,
        .display-mini-table td {
            border: 1px solid #777;
            padding: 4px 5px;
            font-size: .88rem;
        }

        .display-mini-table th {
            background: #fff;
            font-weight: 800;
        }

        @media print {
            .display-toolbar {
                display: none !important;
            }

            .display-sheet {
                padding: 0;
            }

            .display-paper {
                border: none;
                padding: 0;
            }

            .display-watermark {
                opacity: 0.05;
            }
        }

        @media (max-width: 768px) {
            .display-head {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .display-head-logo {
                margin: 0 auto;
            }

            .display-head-qr {
                margin: 0 auto;
            }

            .display-meta-grid {
                grid-template-columns: 1fr;
            }

            .display-assessment-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="display-toolbar no-print">
        <a href="{{ route('academics.results.check') }}" class="btn btn-outline-secondary">
            Back
        </a>
        <form method="GET" action="{{ route('academics.results.check.view') }}" class="d-flex flex-wrap align-items-center gap-2">
            <input
                type="text"
                name="admission_number"
                value="{{ $admissionNumber }}"
                class="form-control"
                placeholder="Admission number (optional)"
                style="min-width: 220px;"
            >
            <input
                type="text"
                name="token"
                value="{{ $token ?? '' }}"
                class="form-control text-uppercase"
                placeholder="Result token"
                maxlength="10"
                style="min-width: 180px;"
                required
            >
            <button type="submit" class="btn btn-success">Check</button>
            @if ($resultSlip)
                <button type="button" onclick="window.print()" class="btn btn-outline-secondary">Print</button>
            @endif
        </form>
    </div>

    <div class="display-sheet">
        @if (!($token ?? ''))
            <div class="sheet-panel text-center py-5">
                <h2 class="h4 mb-3">Enter Result Token</h2>
                <p class="text-secondary mb-0">Use the fields above to look up a published terminal result. Admission number is optional.</p>
            </div>
        @elseif (!$resultSlip)
            <div class="sheet-panel text-center py-5">
                <h2 class="h4 mb-3">No Published Result Found</h2>
                <p class="text-secondary mb-0">We could not find a published result with the supplied token{{ $admissionNumber ? ' and admission number ' . $admissionNumber : '' }}.</p>
            </div>
        @else
            <div class="display-paper">
                <div class="display-watermark">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($organization?->name ?? 'R', 0, 1)) }}</div>

                <div class="display-head">
                    <div>
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="School Logo" class="display-head-logo">
                        @endif
                    </div>
                    <div class="display-school">
                        <h1>{{ \Illuminate\Support\Str::upper($organization?->name ?? config('app.name', 'School')) }}</h1>
                        @if ($addressParts)
                            <p>{{ implode(', ', $addressParts) }}</p>
                        @endif
                        <h2>TERMINAL RESULT</h2>
                    </div>
                    <div class="display-head-qr">
                        @if (!empty($qrSvg))
                            {!! $qrSvg !!}
                        @endif
                        <span>TK: {{ $resultSlip['published_result']['token'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="display-meta">
                    <div class="display-meta-grid">
                        <div class="display-meta-item"><strong>Name:</strong> <span>{{ $resultSlip['student']['full_name'] ?? '—' }}</span></div>
                        <div class="display-meta-item"><strong>Class:</strong> <span>{{ $resultSlip['class']['name'] ?? '—' }}</span></div>
                        <div class="display-meta-item"><strong>Session:</strong> <span>{{ $resultSlip['academic_year']['name'] ?? '—' }}</span></div>
                        <div class="display-meta-item"><strong>Term:</strong> <span>{{ $resultSlip['academic_term']['name'] ?? '—' }}</span></div>
                        <div class="display-meta-item"><strong>Position:</strong> <span>{{ $resultSlip['summary']['position'] ?? '—' }}</span></div>
                        <div class="display-meta-item"><strong>Out Of:</strong> <span>{{ $resultSlip['summary']['out_of'] ?? '—' }}</span></div>
                    </div>
                </div>

                <div class="display-section-title">PERFORMANCE IN SUBJECTS</div>

                <div class="table-responsive">
                    <table class="display-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subjects</th>
                                @foreach ($componentDefinitions as $component)
                                    <th>{{ $component['title'] }}</th>
                                @endforeach
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Rank</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (($resultSlip['assessments'] ?? []) as $index => $assessment)
                                @php
                                    $scores = collect($assessment['components'] ?? [])->mapWithKeys(function ($component) {
                                        $key = trim((string) ($component['code'] ?? $component['label'] ?? ''));
                                        return [$key => number_format((float) ($component['score'] ?? 0), 2)];
                                    });
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="subject-col">{{ \Illuminate\Support\Str::upper($assessment['subject_name'] ?? 'Subject') }}</td>
                                    @foreach ($componentDefinitions as $component)
                                        <td>{{ $scores->get($component['key'], '—') }}</td>
                                    @endforeach
                                    <td>{{ number_format((float) ($assessment['total_score'] ?? 0), 2) }}</td>
                                    <td>{{ \Illuminate\Support\Str::upper($assessment['grade'] ?? '—') }}</td>
                                    <td>
                                        @if (!empty($assessment['subject_rank']))
                                            {{ $assessment['subject_rank'] }}/{{ $assessment['subject_participant_count'] ?? $assessment['subject_rank'] }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $assessment['remark'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="display-summary">
                    <div>NO. SUBJECT OFFERED: {{ $resultSlip['summary']['subject_count'] ?? 0 }}</div>
                    <div>TOTAL SCORE: {{ number_format((float) ($resultSlip['summary']['overall_total'] ?? 0), 2) }}</div>
                    <div>AVERAGE SCORE: {{ number_format((float) ($resultSlip['summary']['average_score'] ?? 0), 2) }}%</div>
                </div>

                <div class="display-section-title">GRADING KEYS</div>
                <table class="display-mini-table">
                    <tr>
                        @forelse (($gradeScales ?? collect()) as $gradeScale)
                            <td>
                                <strong>{{ \Illuminate\Support\Str::upper($gradeScale->code ?: $gradeScale->name) }}</strong>
                                =
                                {{ $gradeScale->description ?: $gradeScale->name }}
                                @if ($gradeScale->min_score !== null || $gradeScale->max_score !== null)
                                    ({{ $gradeScale->min_score !== null ? number_format((float) $gradeScale->min_score, 0) : '0' }}-{{ $gradeScale->max_score !== null ? number_format((float) $gradeScale->max_score, 0) : '100' }})
                                @endif
                            </td>
                        @empty
                            <td>No grading system configured.</td>
                        @endforelse
                    </tr>
                </table>

                <div class="display-assessment-grid">
                    <table class="display-mini-table">
                        <thead>
                            <tr>
                                <th>Effective Assessment</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (($resultSlip['student_ratings']['effective_assessment'] ?? []) as $item)
                                <tr>
                                    <td>{{ $item['label'] }}</td>
                                    <td>{{ $item['value'] ? $item['value'].' '.$item['rating'] : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="display-mini-table">
                        <thead>
                            <tr>
                                <th>Psychomotor Assessment</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (($resultSlip['student_ratings']['psychomotor_assessment'] ?? []) as $item)
                                <tr>
                                    <td>{{ $item['label'] }}</td>
                                    <td>{{ $item['value'] ? $item['value'].' '.$item['rating'] : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
