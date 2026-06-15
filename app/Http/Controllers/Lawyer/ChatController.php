<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $lawyer = auth()->user()->lawyer;

        $conversations = Conversation::query()
            ->where('lawyer_id', $lawyer->id)
            ->with(['client.user', 'latestMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('chat.index', [
            'conversations' => $conversations,
            'role' => 'lawyer',
            'chatRoutePrefix' => 'lawyer.chat',
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizeLawyer($conversation);

        $conversation->load(['lawyer.user', 'client.user']);

        $this->markIncomingAsRead($conversation);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        return view('chat.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherParty' => $conversation->client->user,
            'role' => 'lawyer',
            'chatRoutePrefix' => 'lawyer.chat',
            'backRoute' => route('lawyer.chat.index'),
        ]);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse|JsonResponse
    {
        $this->authorizeLawyer($conversation);

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

        return redirect()->route('lawyer.chat.show', $conversation);
    }

    public function fetch(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeLawyer($conversation);

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

    private function authorizeLawyer(Conversation $conversation): void
    {
        abort_unless(
            auth()->user()->isLawyer()
            && $conversation->lawyer_id === auth()->user()->lawyer->id,
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
