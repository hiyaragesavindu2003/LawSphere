@extends('layouts.app')

@section('title', 'Pending Approval')

@section('content')
<div class="container">
    <div class="card auth-card text-center" style="max-width: 560px;">
        <div class="card-body p-5">
            <i class="bi bi-hourglass-split display-1 text-gold mb-3"></i>
            <h3>Account Pending Approval</h3>
            <p class="text-muted">
                Thank you for registering as a lawyer. An administrator will review your application shortly.
                You will receive access to your dashboard once approved.
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-btn-outline-secondary">Logout</button>
            </form>
        </div>
    </div>
</div>
@endsection
