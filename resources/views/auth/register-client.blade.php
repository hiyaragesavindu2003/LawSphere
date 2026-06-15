@extends('layouts.app')

@section('title', 'Register as Client')

@section('content')
<div class="container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h4 class="mb-0"><i class="bi bi-person-plus me-2"></i>Client Registration</h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('register.client') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                              id="address" name="address" rows="2">{{ old('address') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation"
                           name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-btn-navy w-100">Create Client Account</button>
            </form>
            <hr>
            <p class="text-center mb-0">
                Already registered? <a href="{{ route('login') }}">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
