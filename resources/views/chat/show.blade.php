@extends('layouts.app')

@section('title', 'Chat with ' . $otherParty->name)

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm chat-portal">
        <div class="card-header chat-portal-header d-flex align-items-center gap-3 py-3">
            <a href="{{ $backRoute }}" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-left"></i>
            </a>
            <img src="{{ $otherParty->profile_photo_url }}" alt="{{ $otherParty->name }}"
                 class="chat-list-avatar rounded-circle">
            <div>
                <h6 class="mb-0 text-white">{{ $otherParty->name }}</h6>
                <small class="text-white-50">
                    @if($role === 'client')
                        {{ $conversation->lawyer->specialization }}
                    @else
                        Client
                    @endif
                </small>
            </div>
        </div>

        <div class="card-body chat-messages p-3" id="chatMessages">
            @foreach($messages as $message)
                <div class="chat-bubble-row {{ $message->isFrom(auth()->user()) ? 'mine' : 'theirs' }}"
                     data-message-id="{{ $message->id }}">
                    <div class="chat-bubble">
                        <p class="mb-1">{{ $message->body }}</p>
                        <small class="chat-time">{{ $message->created_at->format('h:i A') }}</small>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card-footer bg-white border-top p-3">
            <form id="chatForm" method="POST" action="{{ route($chatRoutePrefix . '.store', $conversation) }}">
                @csrf
                <div class="input-group">
                    <input type="text" name="body" id="messageInput" class="form-control"
                           placeholder="Type your message..." maxlength="2000" required autocomplete="off">
                    <button type="submit" class="btn btn-btn-navy" id="sendBtn">
                        <i class="bi bi-send"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const messagesEl = document.getElementById('chatMessages');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('messageInput');
    const fetchUrl = @json(route($chatRoutePrefix . '.fetch', $conversation));
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let lastId = 0;
    messagesEl.querySelectorAll('[data-message-id]').forEach(el => {
        const id = parseInt(el.dataset.messageId, 10);
        if (id > lastId) lastId = id;
    });

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendMessage(msg) {
        if (document.querySelector('[data-message-id="' + msg.id + '"]')) return;

        const row = document.createElement('div');
        row.className = 'chat-bubble-row ' + (msg.is_mine ? 'mine' : 'theirs');
        row.dataset.messageId = msg.id;
        row.innerHTML = '<div class="chat-bubble"><p class="mb-1"></p><small class="chat-time"></small></div>';
        row.querySelector('p').textContent = msg.body;
        row.querySelector('.chat-time').textContent = msg.time;
        messagesEl.appendChild(row);
        lastId = Math.max(lastId, msg.id);
        scrollToBottom();
    }

    scrollToBottom();

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const body = input.value.trim();
        if (!body) return;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ body }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.message) appendMessage(data.message);
            input.value = '';
        })
        .catch(() => form.submit());
    });

    setInterval(function () {
        fetch(fetchUrl + '?after_id=' + lastId, {
            headers: { 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            (data.messages || []).forEach(appendMessage);
        });
    }, 4000);
})();
</script>
@endpush
