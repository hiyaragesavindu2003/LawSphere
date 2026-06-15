@extends('layouts.app')

@section('title', 'Request Legal Advice')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lawyers.index') }}">Find a Lawyer</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lawyers.show', $lawyer) }}">{{ $lawyer->user->name }}</a></li>
            <li class="breadcrumb-item active">Request Advice</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm legal-advice-form-card">
                <div class="card-header legal-advice-form-header py-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $lawyer->user->profile_photo_url }}" alt="{{ $lawyer->user->name }}"
                             class="chat-list-avatar rounded-circle">
                        <div>
                            <h4 class="mb-1 text-white">Request Legal Advice</h4>
                            <p class="mb-0 text-white-50">{{ $lawyer->user->name }} — {{ $lawyer->specialization }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('client.legal-advice.store', $lawyer) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">
                                <i class="bi bi-tag text-gold me-1"></i> Subject
                            </label>
                            <input type="text" class="form-control form-control-lg @error('subject') is-invalid @enderror"
                                   id="subject" name="subject" value="{{ old('subject') }}"
                                   placeholder="e.g. Contract review for small business" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                <i class="bi bi-card-text text-gold me-1"></i> Describe your legal question
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="8" required
                                      placeholder="Provide as much detail as possible about your situation...">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-light border mb-0">
                            <i class="bi bi-info-circle text-gold me-1"></i>
                            Your request will be sent to the lawyer after <strong>payment</strong>.
                        </div>

                        <div class="payment-fee-box d-flex justify-content-between align-items-center mt-4 p-3 rounded">
                            <span class="fw-semibold"><i class="bi bi-credit-card text-gold me-1"></i> Advice fee</span>
                            <span class="fs-5 fw-bold text-gold">{{ config('lawsphere.currency_symbol') }}{{ number_format($adviceFee, 2) }}</span>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-btn-gold btn-lg flex-grow-1">
                                <i class="bi bi-send me-1"></i> Submit Request
                            </button>
                            <a href="{{ route('lawyers.show', $lawyer) }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
