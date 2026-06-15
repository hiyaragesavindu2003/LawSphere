@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h4 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In</h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-btn-navy w-100 mb-3">Login</button>
                <div class="text-center">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
            </form>
            <hr>
            <div class="text-center">
                <p class="mb-2">Don't have an account?</p>
                <a href="{{ route('register.client') }}" class="btn btn-btn-outline-primary btn-sm me-2">Register as Client</a>
                <a href="{{ route('register.lawyer') }}" class="btn btn-btn-outline-secondary btn-sm">Register as Lawyer</a>
            </div>
        </div>
    </div>
</div>
@endsection
