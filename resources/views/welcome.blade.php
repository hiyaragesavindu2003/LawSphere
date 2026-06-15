@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 mb-3">Welcome to <span class="text-gold">LawSphere</span></h1>
        <p class="lead mb-4">Your trusted platform connecting clients with qualified legal professionals.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('lawyers.index') }}" class="btn btn-btn-gold btn-lg">Find a Lawyer</a>
            <a href="{{ route('register.lawyer') }}" class="btn btn-btn-outline-light btn-lg">Join as Lawyer</a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-search display-4 text-gold mb-3"></i>
                        <h5>Search Lawyers</h5>
                        <p class="text-muted">Find approved lawyers by specialization and ratings.</p>
                        <a href="{{ route('lawyers.index') }}" class="btn btn-btn-navy btn-sm">Browse Lawyers</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-calendar-check display-4 text-gold mb-3"></i>
                        <h5>Book Appointments</h5>
                        <p class="text-muted">Schedule consultations with ease.</p>
                        @auth
                            @if(auth()->user()->isClient())
                                <a href="{{ route('client.appointments.index') }}" class="btn btn-btn-navy btn-sm">My Appointments</a>
                            @else
                                <a href="{{ route('lawyers.index') }}" class="btn btn-btn-navy btn-sm">Find a Lawyer</a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-btn-navy btn-sm">Login to Book</a>
                        @endauth
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-chat-dots display-4 text-gold mb-3"></i>
                        <h5>Legal Advice</h5>
                        <p class="text-muted">Submit requests and receive professional responses.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
