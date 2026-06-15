@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-chat-dots me-2"></i>Messages</h2>
            <p class="text-muted mb-0">Chat with {{ $role === 'client' ? 'your lawyers' : 'your clients' }}</p>
        </div>
        @if($role === 'client')
            <a href="{{ route('lawyers.index') }}" class="btn btn-btn-gold">
                <i class="bi bi-search me-1"></i> Find a Lawyer
            </a>
        @endif
    </div>

    @if($conversations->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-chat-left-text display-4 text-muted mb-3"></i>
                <h5>No conversations yet</h5>
                <p class="text-muted mb-3">
                    @if($role === 'client')
                        Select a lawyer and click <strong>Start Chat</strong> to begin messaging.
                    @else
                        Clients will appear here when they message you.
                    @endif
                </p>
                @if($role === 'client')
                    <a href="{{ route('lawyers.index') }}" class="btn btn-btn-navy">Browse Lawyers</a>
                @endif
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                @foreach($conversations as $conversation)
                    @php
                        $other = $role === 'client' ? $conversation->lawyer->user : $conversation->client->user;
                        $unread = $conversation->unreadCountFor(auth()->user());
                        $latest = $conversation->latestMessage;
                    @endphp
                    <a href="{{ route($chatRoutePrefix . '.show', $conversation) }}"
                       class="list-group-item list-group-item-action chat-list-item py-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $other->profile_photo_url }}" alt="{{ $other->name }}"
                                 class="chat-list-avatar rounded-circle">
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-0 {{ $unread ? 'fw-bold' : '' }}">{{ $other->name }}</h6>
                                    @if($conversation->last_message_at)
                                        <small class="text-muted">{{ $conversation->last_message_at->diffForHumans() }}</small>
                                    @endif
                                </div>
                                @if($role === 'client')
                                    <small class="text-muted">{{ $conversation->lawyer->specialization }}</small>
                                @endif
                                @if($latest)
                                    <p class="mb-0 text-muted small text-truncate {{ $unread ? 'fw-semibold text-dark' : '' }}">
                                        {{ $latest->sender_id === auth()->id() ? 'You: ' : '' }}{{ Str::limit($latest->body, 60) }}
                                    </p>
                                @endif
                            </div>
                            @if($unread > 0)
                                <span class="badge bg-danger rounded-pill">{{ $unread }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
