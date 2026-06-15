@extends('layouts.app')

@section('title', 'Register as Lawyer')

@section('content')
<div class="container">
    <div class="card auth-card" style="max-width: 640px;">
        <div class="card-header text-center">
            <h4 class="mb-0"><i class="bi bi-briefcase me-2"></i>Lawyer Registration</h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('register.lawyer') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                               id="phone" name="phone" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bar_number" class="form-label">Bar Number</label>
                        <input type="text" class="form-control @error('bar_number') is-invalid @enderror"
                               id="bar_number" name="bar_number" value="{{ old('bar_number') }}" required>
                        @error('bar_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="specialization" class="form-label">Specialization</label>
                    <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                           id="specialization" name="specialization" value="{{ old('specialization') }}"
                           placeholder="e.g. Corporate Law, Family Law" required>
                </div>
                <div class="mb-3">
                    <label for="qualifications" class="form-label"> Qualifications</label>
                    <textarea class="form-control @error('qualifications') is-invalid @enderror"
                              id="qualifications" name="qualifications" rows="2" required>{{ old('qualifications') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="experience_years" class="form-label">Years of Experience</label>
                        <input type="number" class="form-control @error('experience_years') is-invalid @enderror"
                               id="experience_years" name="experience_years" value="{{ old('experience_years', 0) }}"
                               min="0" max="60" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="biography" class="form-label">Biography</label>
                    <textarea class="form-control" id="biography" name="biography" rows="3">{{ old('biography') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation"
                           name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-btn-navy w-100">Submit Lawyer Application</button>
            </form>
            <hr>
            <p class="text-center mb-0 text-muted small">
                Your account requires admin approval before you can access the lawyer dashboard.
            </p>
        </div>
    </div>
</div>
@endsection
