<div x-data="{
    scrollBottom() {
        let container = this.$refs.messagesContainer;
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
}" x-effect="$wire.activeConversationId ? setTimeout(() => scrollBottom(), 50) : null"
    @chat-scrolled-bottom.window="setTimeout(() => scrollBottom(), 50)" x-init="
        const chatChannel = Echo.join('chat');
        
        chatChannel.here((users) => {
            $wire.setOnlineUsers(users, { noprogress: true });
        })
        .joining((user) => {
            $wire.userJoined(user, { noprogress: true });
        })
        .leaving((user) => {
            $wire.userLeft(user, { noprogress: true });
        })
        .listen('.MessageSent', (e) => {
            console.log('EVENT RECEIVED JS:', e);
            $wire.receiveMessage({ noprogress: true });
        })
        .listen('.GlobalMaintenanceEvent', (e) => {
            console.log('Mantenimiento toggleado en chat channel, recargando...', e);
            if (e.sender_id == {{ auth()->user() ? auth()->id() : '0' }}) return;
            window.location.reload();
        });
    " class="fixed bottom-6 right-6 z-50 flex flex-col items-end">

    @if($isOpen) <div
        class="bg-white dark:bg-gray-800 w-80 sm:w-96 h-[500px] mb-4 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden transition-all">
        {{-- Header --}}
        <div
            class="bg-gradient-to-r from-[#13322B] to-[#9b2247] p-4 flex items-center justify-between text-white shrink-0">
            <div class="flex items-center gap-3">
                @if($activeConversationId && $activeUser)
                <button wire:click="closeConversation" class="text-white hover:text-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex items-center gap-2">
                    <x-user-avatar :avatar="$activeUser->avatar" :name="$activeUser->name" size="w-8 h-8"
                        iconSize="w-5 h-5" />
                    <div class="font-bold text-sm truncate max-w-[150px]">{{ $activeUser->name }}</div>
                </div>
                @else
                <div class="font-bold">Chat Interno</div>
                @endif
            </div>
            <button wire:click="toggleChat" class="text-white hover:text-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 flex flex-col" x-ref="messagesContainer">
            @if(!$activeConversationId)
            {{-- List users --}}
            <div class="p-3 shrink-0">
                <input type="text" wire:model.live.debounce.300ms="searchQuery" placeholder="Buscar empleados..."
                    class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:ring-oro focus:border-oro">
            </div>
            <div class="flex-1 overflow-y-auto pb-4">
                @forelse($users as $u)
                <button wire:click="openConversation({{ $u->id }})"
                    class="w-full text-left flex items-center gap-3 p-3 hover:bg-gray-100 dark:hover:bg-gray-800 transition border-b border-white dark:border-gray-800 relative">
                    <div class="relative">
                        <x-user-avatar :avatar="$u->avatar" :name="$u->name" size="w-10 h-10" />
                        <div
                            class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 {{ in_array($u->id, $onlineUsers) ? 'bg-green-500' : 'bg-gray-400' }}">
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 flex justify-between items-center">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ $u->name }}
                            </div>
                            <div
                                class="text-xs {{ in_array($u->id, $onlineUsers) ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                                {{ in_array($u->id, $onlineUsers) ? 'En línea' : 'Desconectado' }}
                            </div>
                        </div>
                        @if(isset($unreadCountsByUser[$u->id]) && $unreadCountsByUser[$u->id] > 0)
                        <div
                            class="ml-2 bg-oro text-[#13322B] text-[10px] font-bold h-5 min-w-[20px] px-1.5 flex items-center justify-center rounded-full shrink-0 shadow-sm border border-white dark:border-gray-800">
                            {{ $unreadCountsByUser[$u->id] }}
                        </div>
                        @endif
                    </div>
                </button>
                @empty
                <div class="p-4 text-center text-gray-500 text-sm italic">
                    No se encontraron empleados.
                </div>
                @endforelse
            </div>
            @else
            {{-- Chat messages --}}
            <div x-init="setTimeout(() => scrollBottom(), 10)" class="p-4 flex flex-col gap-3 mt-auto"
                id="chat-messages-wrapper">
                @forelse($messages as $msg)
                <div wire:key="msg-{{ $msg->id }}"
                    class="flex flex-col {{ $msg->sender_id === auth()->id() ? 'items-end' : 'items-start' }}">
                    <div
                        class="max-w-[75%] px-4 py-2 rounded-2xl text-sm {{ $msg->sender_id === auth()->id() ? 'bg-oro text-[#13322B] rounded-br-sm' : 'bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-bl-sm' }}">
                        {{ $msg->body }}
                    </div>
                    <span class="text-[10px] text-gray-400 mt-1">{{ $msg->created_at->format('H:i') }}</span>
                </div>
                @empty
                <div class="text-center text-xs text-gray-400 my-4">Esta es la historia del chat con {{
                    $activeUser->name }}. Manda un mensaje.</div>
                @endforelse
            </div>
            @endif
        </div>

        {{-- Footer Input --}}
        @if($activeConversationId)
        <div class="p-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <input type="text" wire:model="newMessage" placeholder="Escribe un mensaje..."
                    class="flex-1 text-sm rounded-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-oro focus:border-oro px-4 py-2"
                    required>
                <button type="submit"
                    class="w-10 h-10 rounded-full bg-oro text-white flex items-center justify-center hover:bg-yellow-600 transition disabled:opacity-50">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                    </svg>
                </button>
            </form>
        </div>
        @endif
    </div>
    @endif

    {{-- Floating Button --}}
    <button wire:click="toggleChat"
        class="w-14 h-14 rounded-full bg-gradient-to-br from-[#13322B] to-[#9b2247] text-white flex items-center justify-center shadow-lg hover:shadow-xl hover:scale-105 transition-all relative">
        @if(!$isOpen && $unreadCount > 0)
        <span
            class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-oro text-[10px] font-bold text-[#13322B] border-2 border-white dark:border-gray-900">
            {{ $unreadCount }}
        </span>
        @endif
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>
</div>