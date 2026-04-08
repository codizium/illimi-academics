<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Result Checker'))</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <style>
        :root {
            --sheet-ink: #0f172a;
            --sheet-muted: #475569;
            --sheet-line: #cbd5e1;
            --sheet-bg: #f8fafc;
            --sheet-card: #ffffff;
            --sheet-accent: #0f766e;
        }

        body.result-check-body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.09), transparent 28%),
                radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.08), transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
            color: var(--sheet-ink);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .result-shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 16px 48px;
        }

        .result-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .result-hero {
            padding: 24px 24px 18px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248,250,252,0.96));
        }

        .result-sheet {
            padding: 24px;
        }

        .sheet-panel {
            background: var(--sheet-card);
            border: 1px solid var(--sheet-line);
            border-radius: 18px;
            padding: 18px;
        }

        .sheet-title {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .sheet-subtitle {
            color: var(--sheet-muted);
            font-size: .95rem;
        }

        .sheet-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 28px;
        }

        .sheet-meta-item {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid var(--sheet-line);
            padding-bottom: 6px;
        }

        .sheet-meta-item strong {
            min-width: 96px;
        }

        .sheet-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sheet-table th,
        .sheet-table td {
            border: 1px solid var(--sheet-line);
            padding: 10px 8px;
            font-size: .92rem;
            vertical-align: middle;
        }

        .sheet-table th {
            background: #f1f5f9;
            font-weight: 800;
            text-transform: uppercase;
            font-size: .8rem;
            letter-spacing: 0.03em;
        }

        .sheet-watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            opacity: 0.05;
            font-size: 16rem;
            font-weight: 800;
            color: var(--sheet-accent);
        }

        @media print {
            body.result-check-body {
                background: #fff;
            }

            .result-shell {
                max-width: none;
                padding: 0;
            }

            .result-card {
                border: none;
                box-shadow: none;
                border-radius: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .result-sheet,
            .result-hero {
                padding: 18px;
            }

            .sheet-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="result-check-body">
    <div class="result-shell">
        <div class="result-card">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
