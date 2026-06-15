<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Lawyer;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $client = auth()->user()->client;

        $conversations = Conversation::query()
            ->where('client_id', $client->id)
            ->with(['lawyer.user', 'latestMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('chat.index', [
            'conversations' => $conversations,
            'role' => 'client',
            'chatRoutePrefix' => 'client.chat',
        ]);
    }

    public function start(Lawyer $lawyer): RedirectResponse
    {
        abort_unless($lawyer->is_approved, 404);

        $client = auth()->user()->client;
        $conversation = Conversation::findOrCreateBetween($client, $lawyer);

        return redirect()->route('client.chat.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizeClient($conversation);

        $conversation->load(['lawyer.user', 'client.user']);

        $this->markIncomingAsRead($conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        return view('chat.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherParty' => $conversation->lawyer->user,
            'role' => 'client',
            'chatRoutePrefix' => 'client.chat',
            'backRoute' => route('client.chat.index'),
        ]);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $this->authorizeClient($conversation);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->formatMessage($message),
            ]);
        }

        return redirect()->route('client.chat.show', $conversation);
    }

    public function fetch(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeClient($conversation);

        $afterId = (int) $request->get('after_id', 0);

        $messages = $conversation->messages()
            ->with('sender')
            ->when($afterId > 0, fn ($q) => $q->where('id', '>', $afterId))
            ->orderBy('created_at')
            ->get();

        $this->markIncomingAsRead($conversation);

        return response()->json([
            'messages' => $messages->map(fn ($m) => $this->formatMessage($m)),
        ]);
    }

    private function authorizeClient(Conversation $conversation): void
    {
        abort_unless(
            auth()->user()->isClient()
            && $conversation->client_id === auth()->user()->client->id,
            403
        );
    }

    private function markIncomingAsRead(Conversation $conversation): void
    {
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    private function formatMessage(Message $message): array
    {
        $message->loadMissing('sender');

        return [
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name,
            'is_mine' => $message->sender_id === auth()->id(),
            'created_at' => $message->created_at->format('M d, Y h:i A'),
            'time' => $message->created_at->format('h:i A'),
        ];
    }
}
