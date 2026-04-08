@extends('layouts.app')

@section('content')
    <div class="breadcrumb d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h1 class="fw-semibold mb-4 h6 text-primary-light">Item Analysis</h1>
            <div>
                <a href="/" class="text-secondary-light hover-text-primary hover-underline">Dashboard</a>
                <a href="/academics/exams" class="text-secondary-light hover-text-primary hover-underline">/ Exams</a>
                <span class="text-secondary-light">/ Item Analysis</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-secondary-light mb-0">Select an exam to view difficulty and discrimination index.</p>
        </div>
    </div>
@endsection
