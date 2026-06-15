@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h4 class="mb-0"><i class="bi bi-envelope-check me-2"></i>Verify Your Email</h4>
        </div>
        <div class="card-body p-4 text-center">
            <p>Thanks for signing up! Please verify your email address by clicking the link we sent you.</p>
            <p class="text-muted">If you didn't receive the email, we can send another.</p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-btn-gold">Resend Verification Email</button>
            </form>
        </div>
    </div>
</div>
@endsection
