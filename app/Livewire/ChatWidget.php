<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatWidget extends Component
{
    public $isOpen = false;
    public $activeConversationId = null;
    public $newMessage = '';
    public $searchQuery = '';
    public $onlineUsers = []; // Array of IDs

    protected $listeners = [
        'echo-presence:chat,here' => 'setOnlineUsers',
        'echo-presence:chat,joining' => 'userJoined',
        'echo-presence:chat,leaving' => 'userLeft',
    ];

    public function setOnlineUsers($users)
    {
        $this->onlineUsers = collect($users)->pluck('id')->toArray();
    }

    public function userJoined($user)
    {
        if (!in_array($user['id'], $this->onlineUsers)) {
            $this->onlineUsers[] = $user['id'];
        }
    }

    public function userLeft($user)
    {
        $this->onlineUsers = array_filter($this->onlineUsers, fn($id) => $id != $user['id']);
    }

    public function reloadBecauseMaintenance()
    {
        $this->js('window.location.reload()');
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen && $this->activeConversationId) {
            $this->markAsRead($this->activeConversationId);
        }
    }

    public function openConversation($userId)
    {
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', auth()->id())->where('user_two_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)->where('user_two_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => auth()->id(),
                'user_two_id' => $userId,
            ]);
        }

        $this->activeConversationId = $conversation->id;
        $this->markAsRead($this->activeConversationId);
        $this->dispatch('chat-scrolled-bottom');
    }

    public function closeConversation()
    {
        $this->activeConversationId = null;
    }

    public function sendMessage()
    {
        if (trim($this->newMessage) === '' || !$this->activeConversationId) {
            return;
        }

        Message::create([
            'conversation_id' => $this->activeConversationId,
            'sender_id' => auth()->id(),
            'body' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->dispatch('chat-scrolled-bottom');
    }

    public function markAsRead($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function receiveMessage()
    {
        // Whenever a new message arrives via websockets, we just refresh the component implicitly.
        if ($this->isOpen && $this->activeConversationId) {
            $this->markAsRead($this->activeConversationId);
        }
        $this->dispatch('chat-scrolled-bottom');
    }

    public function render()
    {
        $users = [];
        $unreadCountsByUser = [];

        if (!$this->activeConversationId && $this->isOpen) {
            $query = User::where('id', '!=', auth()->id())
                ->where('active', true)
                ->when($this->searchQuery, function ($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%');
            });

            if (!empty($this->onlineUsers)) {
                $ids = implode(',', array_map('intval', $this->onlineUsers));
                $query->orderByRaw("id IN ($ids) DESC")->orderBy('name', 'asc');
            }
            else {
                $query->orderBy('name', 'asc');
            }

            $users = $query->limit(30)->get();

            // Fetch unread messages count per user
            $unreadCountsList = Message::where('sender_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->whereIn('conversation_id', function ($query) {
                $query->select('id')
                    ->from('conversations')
                    ->where('user_one_id', auth()->id())
                    ->orWhere('user_two_id', auth()->id());
            })
                ->select('sender_id', DB::raw('count(*) as aggregate'))
                ->groupBy('sender_id')
                ->pluck('aggregate', 'sender_id')
                ->toArray();

            $unreadCountsByUser = $unreadCountsList;
        }

        $messages = [];
        $activeUser = null;

        if ($this->activeConversationId) {
            $conversation = Conversation::find($this->activeConversationId);
            if ($conversation) {
                // Determine the other user id
                $otherUserId = $conversation->user_one_id === auth()->id() ? $conversation->user_two_id : $conversation->user_one_id;
                // Important: Since we can't join perfectly across DBs easily in eloquent like this, we fetch the model from the primary DB
                $activeUser = User::find($otherUserId);
                $messages = Message::where('conversation_id', $this->activeConversationId)->orderBy('created_at', 'asc')->get();
            }
        }

        // Count total unread messages across all conversations
        $unreadCount = Message::where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->whereIn('conversation_id', function ($query) {
            $query->select('id')
                ->from('conversations')
                ->where('user_one_id', auth()->id())
                ->orWhere('user_two_id', auth()->id());
        })->count();

        return view('livewire.chat-widget', [
            'users' => $users,
            'messages' => $messages,
            'activeUser' => $activeUser,
            'unreadCount' => $unreadCount,
            'unreadCountsByUser' => $unreadCountsByUser,
        ]);
    }
}