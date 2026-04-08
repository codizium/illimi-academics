@extends('illimi-academics::frontend.layout')

@php
    $logoUrl = null;
    if ($organization && method_exists($organization, 'hasAttachment') && $organization->hasAttachment('logo')) {
        $logoUrl = $organization->getAttachmentUrl('logo');
    }
@endphp

@section('title', 'Result Checker')

@push('styles')
    <style>
        .landing-wrap {
            min-height: 72vh;
            display: grid;
            place-items: center;
        }

        .landing-card {
            width: 100%;
            max-width: 540px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.10);
            padding: 36px 28px;
            text-align: center;
        }

        .landing-logo {
            width: 92px;
            height: 92px;
            object-fit: contain;
            margin-bottom: 16px;
        }

        .landing-mark {
            width: 92px;
            height: 92px;
            margin: 0 auto 16px;
            border-radius: 24px;
            display: grid;
            place-items: center;
            font-size: 2rem;
            color: #fff;
            background: linear-gradient(135deg, #0f766e, #2563eb);
            box-shadow: 0 20px 45px rgba(37, 99, 235, 0.24);
        }

        .landing-title {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            margin-bottom: 6px;
        }

        .landing-subtitle {
            color: var(--sheet-muted);
            margin-bottom: 24px;
        }
    </style>
@endpush

@section('content')
    <div class="result-sheet">
        <div class="landing-wrap">
            <div class="landing-card">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="School Logo" class="landing-logo">
                @else
                    <div class="landing-mark">
                        <i class="ri-graduation-cap-line"></i>
                    </div>
                @endif

                <div class="landing-title">{{ $organization?->name ?? config('app.name', 'School Portal') }}</div>
                <p class="landing-subtitle">Check published terminal results with the result token. Admission number is optional.</p>

                <form method="GET" action="{{ route('academics.results.check.view') }}" class="d-grid gap-3">
                    <div class="text-start">
                        <label class="form-label fw-semibold">Admission Number</label>
                        <input
                            type="text"
                            name="admission_number"
                            value="{{ $admissionNumber }}"
                            class="form-control form-control-lg"
                            placeholder="Enter student admission number (optional)"
                        >
                    </div>
                    <div class="text-start">
                        <label class="form-label fw-semibold">Result Token</label>
                        <input
                            type="text"
                            name="token"
                            value="{{ $token ?? '' }}"
                            class="form-control form-control-lg text-uppercase"
                            placeholder="Enter 8 digits and 2 letters"
                            maxlength="10"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-success btn-lg">
                        Check Result
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
