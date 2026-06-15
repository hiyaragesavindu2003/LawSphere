@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container">
    <div class="card auth-card">
        <div class="card-header text-center">
            <h4 class="mb-0">Change Password</h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                           id="current_password" name="current_password" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="password_confirmation"
                           name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-btn-navy w-100">Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
