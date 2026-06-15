@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h4 class="mb-0">Reset Password</h4>
        </div>
        <div class="card-body p-4">
            <p class="text-muted">Enter your email and we'll send you a reset link.</p>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-btn-navy w-100">Send Reset Link</button>
            </form>
        </div>
    </div>
</div>
@endsection
